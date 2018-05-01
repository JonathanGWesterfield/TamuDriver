#include <Wire.h>
#include <LiquidCrystal_I2C.h>
// Set the LCD I2C address and pin config (may need to change 3 to 8 depending on I2C board model)
LiquidCrystal_I2C lcd(0x27, 2, 1, 0, 4, 5, 6, 7, 3, POSITIVE);

//Variables for setting lot number
const int button1Pin = 4;
const int button2Pin = 5;
const int switchPin = 6;
int btn1State = 0;
int btn2State = 0;
int prevBtn1 = 0;
int prevBtn2 = 0;
int switchState = 0;
int prevSwState = 0;
int lotNum = 0;
int tens = 0;
int ones = 0;
char lotNumBuf[16];
char serialBuf[10];

//Variables for sensors
int ledPin = 13;  // LEDs for debugging
int ledPin2 = 12;
int inputPin1 = 2;  // Sensor pins
int inputPin2 = 3;
int pirState = LOW; // Initial sensor states
int pirState2 = LOW;
int val = 0;
int val2 = 0;
int hit1 = 0, hit2 = 0;

unsigned long timeout;

/**
  Setup function which initializes the LCD
  and sets the modes for all necessary pins.
  @return void
*/
void setup()
{
  pinMode(ledPin, OUTPUT);
  pinMode(ledPin2, OUTPUT);
  pinMode(inputPin1, INPUT);
  pinMode(inputPin2, INPUT);
  Serial.begin(9600);
  lcd.begin(16, 2);
  lcd.clear();
  lcd.backlight();
  lcd.print("Car Counter v1.5");
  delay(2000);
}

/**
  Main loop function which either sets the lot number or reads
  from the sensors depending on the state of the switch.
  @return void
*/
void loop()
{
  prevSwState = switchState;
  switchState = digitalRead(switchPin);

  //Set the lot number if triggered by the switch or no lot number is set
  if (switchState == LOW || lotNum == 0) {
    setLotNum();
  } else {
    if (prevSwState == LOW) {
      //Since println doesn't support formatting, send formatted string to buffer and send it over the serial port.
      sprintf(serialBuf, "lot%d", lotNum);
      Serial.println(serialBuf);
    }
    readSensors();
    lcd.clear();
    lcd.print("Counting cars...");
  }
}

/**
  This function reads from the sensors and sends a signal to the serial port
  for each entry/exit. LEDs for each sensor are activated when motion is detected.
  @return void
*/
void readSensors() {
  lcd.clear();
  val = digitalRead(inputPin1);
  val2 = digitalRead(inputPin2);

  if (val == HIGH) {
    digitalWrite(ledPin, HIGH);
    if (pirState == LOW) {
      // Sensor tripped
      Serial.println("Entering...");
      lcd.print("Entering...");
      hit1 = 1;
      pirState = HIGH;
      timeout = millis() + 2000;
    }
  } else {
    digitalWrite(ledPin, LOW);
    if (pirState == HIGH) {
      // Reset state once motion stops
      pirState = LOW;
    }
  }

  if (val2 == HIGH) {
    digitalWrite(ledPin2, HIGH);
    if (pirState2 == LOW) {
      // Sensor tripped
      Serial.println("Exiting...");
      lcd.print("Exiting...");
      hit2 = 1;
      pirState2 = HIGH;
      timeout = millis() + 2000;
    }
  } else {
    digitalWrite(ledPin2, LOW);
    if (pirState2 == HIGH) {
      // Reset state once motion stops
      pirState2 = LOW;
    }
  }

  while (hit1 == 1 && hit2 == 0 && millis() < timeout) {
    val2 = digitalRead(inputPin2);
    //check sensor 2
    if (val2 == HIGH) {
      digitalWrite(ledPin2, HIGH);
      if (pirState2 == LOW) {
        // Other sensor has tripped
        Serial.println("Entered");
        lcd.print("Entered!");
        pirState2 = HIGH;
        hit1 = 0;
        hit2 = 0;
        delay(2000);
      }
    }
  }

  while (hit1 == 0 && hit2 == 1 && millis() < timeout) {
    val = digitalRead(inputPin1);
    //check sensor 1
    if (val == HIGH) {
      digitalWrite(ledPin, HIGH);
      if (pirState == LOW) {
        // Other sensor has tripped
        Serial.println("Exited");
        lcd.print("Exited!");
        pirState = HIGH;
        hit1 = 0;
        hit2 = 0;
        delay(2000);
      }
    }
  }
}
/**
  This function sets the parking lot number to be used in the database.
  It uses 2 buttons to set the lot number and a switch to set or "unset"
  the current lot.
  @return void
*/
void setLotNum() {
  if (prevSwState == HIGH) {
    tens = 0;
    ones = 0;
  }
  prevBtn1 = btn1State;
  prevBtn2 = btn2State;
  btn1State = digitalRead(button1Pin);
  btn2State = digitalRead(button2Pin);
  //Increment values based on which button is pressed.
  if (btn1State == HIGH && prevBtn1 == LOW) {
    if (tens == 13) {
      //Reset if value gets too high (max of 122 parking lots)
      tens = 0;
    } else {
      tens++;
    }
  }
  if (btn2State == HIGH && prevBtn2 == LOW) {
    if (ones == 9) {
      //Reset if value gets too high
      ones = 0;
    } else {
      ones++;
    }
  }
  lotNum = (10 * tens) + ones;
  if (switchState == HIGH) {
    //Warn if switch is high. Otherwise it reads from sensors as soon as lotNum > 0
    lcd.print("Flip switch 1st!");
  }
  sprintf(lotNumBuf, "Lot #: %d", lotNum);
  lcd.clear();
  lcd.setCursor(0, 1);
  lcd.print(lotNumBuf);
}

