#Smart Car Parking System using ESP32 and Arduino Cloud

This project demonstrates a Smart Parking Monitoring System using an ESP32 microcontroller, Infrared (IR) sensor, and Arduino Cloud to efficiently manage and monitor parking spaces in real-time.

System Overview:

1.An IR sensor is connected to the ESP32 to detect the presence of a vehicle in a parking slot.

2.Upon detection, the ESP32 updates the status of the slot.

3.The ESP32 board connects to the internet via built-in Wi-Fi and sends data to the Arduino Cloud.

4.The data is visualized through a user dashboard, displaying the real-time status of parking slots (Occupied/Vacant).

Project Phases:
 
Phase 1: Object Detection & Status Display

Task: Interface the IR sensor to detect vehicle presence and use LEDs to display slot status.

Goal: Illuminate LEDs to indicate whether a parking slot is occupied or free.

Phase 2: Server-Side Scripting

Task: Develop scripts to store and retrieve parking slot data on the server.

Goal: Enable communication between ESP32 and server (or cloud) for persistent data logging and retrieval.

Phase 3: User Dashboard Development

Task: Create a dashboard for:

Viewing parking slot statuses

Adding new parking places

Sending parking requests

Goal: Provide an interactive and user-friendly interface for real-time monitoring and management.

Setup Instructions:

1.Connect the IR sensor to the ESP32 board.

2.Install the Arduino IDE on your system.

3.Create an account on Arduino Cloud.

4.Install the required Arduino Cloud libraries in the IDE.

5.Open the project code in the Arduino IDE.

6.Configure your Wi-Fi and Arduino Cloud credentials in the code.

7.Upload the code to your ESP32 board.

8.Access the Arduino Cloud dashboard and navigate to the "Parking" tab.

9.View the real-time status of all parking slots.

Key Features:

1.Real-Time Monitoring: Instantly shows the occupancy status of parking slots.

2.Cloud Integration: Data is synced with Arduino Cloud for easy remote access.

3.Web/Mobile Accessibility: Dashboard can be accessed from any device with internet access.

4.Scalability: Ideal for malls, offices, and garages to manage multiple slots.

Benefits:

1.Improved Parking Efficiency: Reduces time spent searching for parking.

2.Reduced Traffic Congestion: Minimizes unnecessary vehicle movement in parking lots.

3.Improved Air Quality: Decreases emissions by reducing idling time.

4.Remote Access: Real-time status available anywhere via cloud dashboard.
