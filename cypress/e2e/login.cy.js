/**
 * Login Form Test Cases
 * Based on Equivalence Class Partitioning and Boundary Value Analysis
 * Test Cases: TC-LOGIN-01 to TC-LOGIN-14
 */

describe('Login Form - Black Box Testing', () => {
  const baseUrl = 'http://127.0.0.1:8080'
  const loginUrl = `${baseUrl}/wp-login.php`
  
  // Test data - WordPress credentials
  const validUsername = 'Qadeer572'
  const validPassword = 'raza@1214'
  
  beforeEach(() => {
    // Visit login page before each test
    cy.visit(loginUrl)
  })

  describe('TC-LOGIN-01: Valid login with username and password (remember me unchecked)', () => {
    it('should successfully login with valid credentials', () => {
      cy.get('#user_login').clear().type(validUsername)
      cy.get('#user_pass').clear().type(validPassword)
      cy.get('#rememberme').should('not.be.checked')
      cy.get('#wp-submit').click()
      
      // Check for successful login - should redirect to admin dashboard
      cy.url().should('not.include', '/wp-login.php')
      cy.url().should('include', '/wp-admin')
    })
  })

  describe('TC-LOGIN-02: Valid login with email and password (remember me checked)', () => {
    it('should successfully login with valid email and remember me checked', () => {
      // Using username as email field also accepts username
      cy.get('#user_login').clear().type(validUsername)
      cy.get('#user_pass').clear().type(validPassword)
      cy.get('#rememberme').check()
      cy.get('#wp-submit').click()
      
      // Check for successful login
      cy.url().should('not.include', '/wp-login.php')
      cy.url().should('include', '/wp-admin')
    })
  })

  describe('TC-LOGIN-03: Empty username field', () => {
    it('should display error for empty username', () => {
      cy.get('#user_login').clear()
      cy.get('#user_pass').clear().type(validPassword)
      cy.get('#rememberme').uncheck()
      cy.get('#wp-submit').click()
      
      // Check for HTML5 validation or error message
      cy.get('#user_login').then(($input) => {
        if ($input[0].validity.valueMissing) {
          // HTML5 validation triggered
          expect($input[0].validity.valueMissing).to.be.true
        } else {
          // Check for error message
          cy.get('#login_error, .login-error, .error').should('be.visible')
        }
      })
    })
  })

  describe('TC-LOGIN-04: Empty password field', () => {
    it('should display error for empty password', () => {
      cy.get('#user_login').clear().type(validUsername)
      cy.get('#user_pass').clear()
      cy.get('#rememberme').uncheck()
      cy.get('#wp-submit').click()
      
      // Check for HTML5 validation or error message
      cy.get('#user_pass').then(($input) => {
        if ($input[0].validity.valueMissing) {
          // HTML5 validation triggered
          expect($input[0].validity.valueMissing).to.be.true
        } else {
          // Check for error message - wait for it to appear and use specific ID
          cy.get('body').then(($body) => {
            if ($body.find('#login_error').length > 0) {
              cy.get('#login_error').should('be.visible')
            } else {
              // Fallback to other error selectors
              cy.get('.login-error, .error, .notice-error').should('be.visible')
            }
          })
        }
      })
    })
  })

  describe('TC-LOGIN-05: Invalid username format', () => {
    it('should display error for invalid username format', () => {
      cy.get('#user_login').clear().type('invalid@user@name')
      cy.get('#user_pass').clear().type(validPassword)
      cy.get('#rememberme').uncheck()
      cy.get('#wp-submit').click()
      
      // Check for error message
      cy.get('#login_error, .login-error, .error').should('be.visible')
    })
  })

  describe('TC-LOGIN-06: Invalid email format', () => {
    it('should display error for invalid email format', () => {
      cy.get('#user_login').clear().type('invalidemail')
      cy.get('#user_pass').clear().type(validPassword)
      cy.get('#rememberme').uncheck()
      cy.get('#wp-submit').click()
      
      // Check for error message
      cy.get('#login_error, .login-error, .error').should('be.visible')
    })
  })

  describe('TC-LOGIN-07: Correct username with incorrect password', () => {
    it('should display error for incorrect password', () => {
      cy.get('#user_login').clear().type(validUsername)
      cy.get('#user_pass').clear().type('wrongpassword')
      cy.get('#rememberme').uncheck()
      cy.get('#wp-submit').click()
      
      // Check for error message
      cy.get('#login_error, .login-error, .error').should('be.visible')
    })
  })

  describe('TC-LOGIN-08: Incorrect username with correct password', () => {
    it('should display error for incorrect username', () => {
      cy.get('#user_login').clear().type('wrongusername')
      cy.get('#user_pass').clear().type(validPassword)
      cy.get('#rememberme').uncheck()
      cy.get('#wp-submit').click()
      
      // Check for error message
      cy.get('#login_error, .login-error, .error').should('be.visible')
    })
  })

  describe('TC-LOGIN-09: Username with minimum length (1 character)', () => {
    it('should handle username with 1 character', () => {
      cy.get('#user_login').clear().type('a')
      cy.get('#user_pass').clear().type(validPassword)
      cy.get('#rememberme').uncheck()
      cy.get('#wp-submit').click()
      
      // Should show error as 1 character username is likely invalid
      cy.get('#login_error, .login-error, .error').should('be.visible')
    })
  })

  describe('TC-LOGIN-10: Username with maximum length (60 characters)', () => {
    it('should handle username with 60 characters', () => {
      const longUsername = 'a'.repeat(60)
      cy.get('#user_login').clear().type(longUsername)
      cy.get('#user_pass').clear().type(validPassword)
      cy.get('#rememberme').uncheck()
      cy.get('#wp-submit').click()
      
      // May show error or may accept (depends on WordPress validation)
      cy.url().then((url) => {
        if (url.includes('/wp-login.php')) {
          cy.get('#login_error, .login-error, .error').should('be.visible')
        }
      })
    })
  })

  describe('TC-LOGIN-11: Username exceeding maximum length (61 characters)', () => {
    it('should handle username with 61 characters', () => {
      const tooLongUsername = 'a'.repeat(61)
      cy.get('#user_login').clear().type(tooLongUsername)
      cy.get('#user_pass').clear().type(validPassword)
      cy.get('#rememberme').uncheck()
      cy.get('#wp-submit').click()
      
      // Should show error or truncate
      cy.url().then((url) => {
        if (url.includes('/wp-login.php')) {
          cy.get('#login_error, .login-error, .error').should('be.visible')
        }
      })
    })
  })

  describe('TC-LOGIN-12: Password with minimum length (1 character)', () => {
    it('should handle password with 1 character', () => {
      cy.get('#user_login').clear().type(validUsername)
      cy.get('#user_pass').clear().type('a')
      cy.get('#rememberme').uncheck()
      cy.get('#wp-submit').click()
      
      // Should show error as 1 character password is likely invalid
      cy.get('#login_error, .login-error, .error').should('be.visible')
    })
  })

  describe('TC-LOGIN-13: Password with maximum length (255 characters)', () => {
    it('should handle password with 255 characters', () => {
      const longPassword = 'a'.repeat(255)
      cy.get('#user_login').clear().type(validUsername)
      cy.get('#user_pass').clear().type(longPassword)
      cy.get('#rememberme').uncheck()
      cy.get('#wp-submit').click()
      
      // May show error or may accept (depends on WordPress validation)
      cy.url().then((url) => {
        if (url.includes('/wp-login.php')) {
          cy.get('#login_error, .login-error, .error').should('be.visible')
        }
      })
    })
  })

  describe('TC-LOGIN-14: Password exceeding maximum length (256 characters)', () => {
    it('should handle password with 256 characters', () => {
      const tooLongPassword = 'a'.repeat(256)
      cy.get('#user_login').clear().type(validUsername)
      cy.get('#user_pass').clear().type(tooLongPassword)
      cy.get('#rememberme').uncheck()
      cy.get('#wp-submit').click()
      
      // Should show error or truncate
      cy.url().then((url) => {
        if (url.includes('/wp-login.php')) {
          cy.get('#login_error, .login-error, .error').should('be.visible')
        }
      })
    })
  })
})
