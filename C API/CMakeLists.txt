cmake_minimum_required(VERSION 3.7)
project(C_API)

set(CMAKE_C_STANDARD 11)
set(SOURCE_FILES main.c Connect.c)
add_executable(C_API ${SOURCE_FILES})