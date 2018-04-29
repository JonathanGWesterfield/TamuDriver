/*
 * PIR sensor tester
 */
 
int ledPin = 13;  // LEDs for debugging               
int ledPin2 = 12;                
int inputPin1 = 2;  // Sensor pins
int inputPin2 = 3;               
int pirState = LOW; // Initial sensor states
int pirState2 = LOW;             
int val = 0;                    
int val2 = 0;                    
int hit1 = 0, hit2 = 0;
 
void setup() {
  pinMode(ledPin, OUTPUT);      
  pinMode(ledPin2, OUTPUT);      
  pinMode(inputPin1, INPUT);    
  pinMode(inputPin2, INPUT);     
  Serial.begin(9600);
}
 
void loop(){
  val = digitalRead(inputPin1);  
  val2 = digitalRead(inputPin2);
  
  if (val == HIGH) {            
    digitalWrite(ledPin, HIGH);  
    if (pirState == LOW) {
      // Sensor tripped
      Serial.println("Entering...");
      hit1 = 1; 
      pirState = HIGH;
    }
  } else {
    digitalWrite(ledPin, LOW); 
    if (pirState == HIGH){
      pirState = LOW; // Reset state once motion stops
    }
  }
  
  if (val2 == HIGH) {           
    digitalWrite(ledPin2, HIGH); 
    if (pirState2 == LOW) {
      // Sensor tripped
      Serial.println("Exiting...");
      hit2 = 1;
      pirState2 = HIGH;
    }
  } else {
    digitalWrite(ledPin2, LOW); 
    if (pirState2 == HIGH){
      pirState2 = LOW; // Reset state once motion stops
    }
  }  
  
  while(hit1 == 1 && hit2 == 0){
    val2 = digitalRead(inputPin2);
    //check sensor 2 
    if (val2 == HIGH) { 
      digitalWrite(ledPin2, HIGH);  
      if (pirState2 == LOW) {
      // Other sensor has tripped
      Serial.println("Entered");
      pirState2 = HIGH;
      hit1 = 0;
      hit2 = 0;
      }
    }
  }
  
  while (hit1 == 0 && hit2 == 1){
    val = digitalRead(inputPin1);
    //check sensor 1 
    if (val == HIGH) {
      digitalWrite(ledPin, HIGH); 
      if (pirState == LOW) {
      // Other sensor has tripped
      Serial.println("Exited");
      pirState = HIGH;
      hit1 = 0;
      hit2 = 0;
      }
    } 
  }
}
