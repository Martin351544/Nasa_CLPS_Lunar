# NASA CLPS Lunar

## Overview

NASA CLPS Lunar is a project developed for the **NASA Space Apps Challenge 2026**.

The project interacts with NASA services through HTTP requests to retrieve relevant lunar mission information and process the returned data.

It uses a combination of PHP and C++ to handle application logic, communicate with external services and present information to the user.

## Features

- **NASA API integration:** Sends HTTP requests to NASA services to retrieve information.
- **Data processing:** Uses C++ to process retrieved information.
- **Web interface:** Uses PHP to provide an interface for interacting with the application.
- **HTTP communication:** Communicates with external NASA services and handles their responses.

## Technologies Used

| Technology | Purpose |
|---|---|
| C++ | Application logic and data processing |
| PHP | Backend and web interface |
| libcurl | HTTP requests |
| nlohmann/json | JSON parsing and handling |

## Requirements

- PHP 8.5 or compatible version
- C++ compiler supporting C++17 or newer
- libcurl development libraries
- nlohmann/json
- Internet connection

## Installation

### 1. Clone the repository

git clone https://github.com/Martin351544/Nasa_CLPS_Lunar.git
cd Nasa_CLPS_Lunar

### 2. Install dependencies

Install PHP, a C++ compiler, libcurl and nlohmann/json using your operating system's package manager.

### 3. Compile the C++ application

Compile the C++ source files with the required libraries.

Refer to the source files and build configuration for the exact compilation requirements.

### 4. Start the PHP server

From the directory containing the PHP entry point, run:

php -S localhost:8000

### 5. Open the application

Navigate to:

http://localhost:8000

An active internet connection is required for communication with NASA services.

## How It Works

1. The user interacts with the PHP web interface.
2. The application processes the user's input.
3. C++ handles the relevant application logic.
4. HTTP requests are sent to NASA services.
5. The returned data is processed and used by the application.
6. Results are presented to the user.

## NASA Resources

- [NASA Open Data Portal](https://data.nasa.gov/)
- [NASA Commercial Lunar Payload Services (CLPS)](https://www.nasa.gov/humans-in-space/commercial-lunar-payload-services/)

## Project Information

Developed for the NASA Space Apps Challenge 2026.

## License

See the LICENSE file for licensing information.

