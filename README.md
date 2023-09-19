# Testreveal - Test Cost Calculator

## Table of Contents
- [Introduction](#introduction)
- [Technologies Used](#technologies-used)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [API Documentation](#api-documentation)
- [Contributing](#contributing)
- [License](#license)

## Introduction
Testreveal is a test cost calculator project that estimates the cost of developing software based on user inputs. It utilizes the Function Point Algorithm (FPA) to provide accurate cost estimates for software development projects. This project also includes features for generating cost estimation reports in PDF format. Additionally, it integrates various third-party services such as IP Stack for getting IP addresses, SendGrid for email notifications, Twilio for WhatsApp notifications, and TextLocal for text SMS notifications.

## Technologies Used
- Node.js
- Express.js
- EJS (Embedded JavaScript)
- MySQL

## Features
1. **API Development:** Designed and developed the API using Node.js and Express.js to facilitate cost estimation calculations.

2. **Cost Estimation:** Implemented a company's algorithm based on the Function Point Algorithm (FPA) to estimate the cost of software development projects. Users can input project details, and the system will provide cost estimates.

3. **Cost PDF Generation:** Utilized EJS to generate cost estimation reports in PDF format, making it easy to share and archive project cost details.

4. **Third-Party Services Integration:**
   - **IP Stack:** Integrated IP Stack to retrieve IP addresses for location-based information.
   - **SendGrid:** Used SendGrid for email notifications and communication.
   - **Twilio:** Integrated Twilio for WhatsApp notifications to keep stakeholders informed.
   - **TextLocal:** Utilized TextLocal for sending text SMS notifications to users.

## Installation
To run this project locally, follow these steps:

1. Clone the repository:
   ```
   git clone https://github.com/Tohid4641/testreveal-test-cost-calculator.git
   ```

2. Navigate to the project directory:
   ```
   cd testreveal-test-cost-calculator
   ```

3. Install the dependencies:
   ```
   npm install
   ```

4. Set up a MySQL database and configure the connection in the project's configuration file.

5. Run the application:
   ```
   npm start
   ```

## Usage
To use Testreveal, access the application via a web browser or make API requests as needed. Provide project details to get accurate cost estimates. Generate cost estimation reports in PDF format for reference and sharing.

## Application URL
Visit [Testreveal](https://testreveal.com/) to access the application online.

<!-- ## API Documentation
For detailed API documentation and usage examples, please refer to the [API Documentation](api-documentation.md) file in the project repository.

## Contributing
Contributions to this project are welcome. Please follow the [Contributing Guidelines](CONTRIBUTING.md) in the repository for more information.

## License
This project is licensed under the [MIT License](LICENSE). You are free to use, modify, and distribute this software as per the terms of the license. -->
