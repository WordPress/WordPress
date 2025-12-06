// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })

// Custom command to login to WordPress
Cypress.Commands.add('wpLogin', (username, password, rememberMe = false) => {
  cy.visit('/wp-login.php')
  cy.get('#user_login').clear().type(username)
  cy.get('#user_pass').clear().type(password)
  if (rememberMe) {
    cy.get('#rememberme').check()
  } else {
    cy.get('#rememberme').uncheck()
  }
  cy.get('#wp-submit').click()
})

// Custom command to check for error messages
Cypress.Commands.add('checkLoginError', (expectedError) => {
  cy.get('#login_error, .login-error, .error').should('be.visible')
  if (expectedError) {
    cy.get('#login_error, .login-error, .error').should('contain', expectedError)
  }
})

// Custom command to check successful login
Cypress.Commands.add('checkLoginSuccess', () => {
  cy.url().should('not.include', '/wp-login.php')
  cy.url().should('include', '/wp-admin')
})

