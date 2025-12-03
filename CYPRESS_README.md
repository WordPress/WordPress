# Cypress Testing Setup for WordPress SQE Project

## Prerequisites
- Node.js and npm installed
- WordPress running on `http://127.0.0.1:8080`
- Valid WordPress admin credentials

## Setup Instructions

1. **Install Dependencies** (Already done)
   ```bash
   npm install
   ```

2. **Update Test Credentials**
   - Open `cypress/e2e/login.cy.js`
   - Update the following variables with your actual WordPress credentials:
     ```javascript
     const validUsername = 'your_admin_username'
     const validPassword = 'your_admin_password'
     const validEmail = 'your_admin_email@example.com'
     ```

## Running Tests

### Open Cypress Test Runner (Interactive Mode)
```bash
npm run cypress:open
```
This opens the Cypress Test Runner GUI where you can:
- Select and run individual tests
- See test execution in real-time
- Debug tests interactively

### Run All Login Tests (Headless Mode)
```bash
npm run test:login
```

### Run All Cypress Tests
```bash
npm run cypress:run
```

## Test Structure

The login test file (`cypress/e2e/login.cy.js`) contains 19 test cases covering:
- Valid login scenarios
- Invalid input scenarios
- Boundary value testing
- Security testing (SQL injection, XSS)
- Remember me checkbox testing

## Test Results

- **Videos**: Test execution videos are saved in `cypress/videos/`
- **Screenshots**: Failure screenshots are saved in `cypress/screenshots/`

## Troubleshooting

1. **WordPress not accessible**: Ensure WordPress is running on `http://127.0.0.1:8080`
2. **Login failures**: Verify your credentials in the test file
3. **Timeout errors**: Increase timeout values in `cypress.config.js` if needed

