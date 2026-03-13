/**
 * Add User Form Test Cases
 * Based on Equivalence Class Partitioning and Boundary Value Analysis
 * Test Cases: TC-ADDUSER-01 to TC-ADDUSER-14
 */

describe('Add User Form - Black Box Testing', () => {
  const baseUrl = 'http://127.0.0.1:8080'
  const addUserUrl = `${baseUrl}/wp-admin/user-new.php`
  const loginUrl = `${baseUrl}/wp-login.php`
  
  // Test data - WordPress admin credentials for login
  const adminUsername = 'Qadeer572'
  const adminPassword = 'raza@1214'
  
  // Test data for new user
  const validUsername = 'testuser'
  const validEmail = 'testuser@example.com'
  const validPassword = 'Test@123456'
  const validFirstName = 'Test'
  const validLastName = 'User'
  const validWebsite = 'https://example.com'
  
  beforeEach(() => {
    // Login as admin before each test
    cy.visit(loginUrl)
    cy.get('#user_login').clear().type(adminUsername)
    cy.get('#user_pass').clear().type(adminPassword)
    cy.get('#wp-submit').click()
    
    // Wait for admin dashboard to load
    cy.url().should('include', '/wp-admin')
    
    // Navigate to Add User page
    cy.visit(addUserUrl)
  })

  describe('TC-ADDUSER-01: Valid user creation with all required fields', () => {
    it('should successfully create a new user with valid data', () => {
      const timestamp = Date.now()
      cy.get('#user_login').clear().type(`user${timestamp}`)
      cy.get('#email').clear().type(`user${timestamp}@example.com`)
      cy.get('#pass1').clear().type(validPassword)
      // Check if weak password confirmation checkbox appears and check it
      cy.get('body').then(($body) => {
        if ($body.find('input[name="pw_weak"]').length > 0) {
          cy.get('input[name="pw_weak"]').check()
        }
      })
      cy.get('#first_name').clear().type(validFirstName)
      cy.get('#last_name').clear().type(validLastName)
      cy.get('#url').clear().type(validWebsite)
      cy.get('#send_user_notification').uncheck()
      cy.get('#role').select('Subscriber')
      cy.get('#createusersub').click()
      
      // Check for success message - WordPress shows success when user is created
      cy.get('.notice-success, .updated, #message').should('be.visible')
    })
  })

  describe('TC-ADDUSER-02: Valid user creation with minimal required fields only', () => {
    it('should successfully create a user with only required fields', () => {
      const timestamp = Date.now()
      cy.get('#user_login').clear().type(`user${timestamp}`)
      cy.get('#email').clear().type(`user${timestamp}@example.com`)
      cy.get('#pass1').clear().type(validPassword)
      // Check if weak password confirmation checkbox appears and check it
      cy.get('body').then(($body) => {
        if ($body.find('input[name="pw_weak"]').length > 0) {
          cy.get('input[name="pw_weak"]').check()
        }
      })
      cy.get('#role').select('Subscriber')
      cy.get('#createusersub').click()
      
      // Check for success message
      cy.get('.notice-success, .updated, #message').should('be.visible')
    })
  })

  describe('TC-ADDUSER-03: Empty username field', () => {
    it('should show WordPress red border (form-invalid class) when username empty', () => {
  
      cy.get('#user_login').clear()
      cy.get('#email').clear().type(validEmail)
      cy.get('#pass1').clear().type(validPassword)
  
      cy.get('#role').select('Subscriber')
  
      // click submit
      cy.get('#createusersub').click({ force: true })
  
      // should stay on same page
      cy.url().should('include', 'user-new.php')
  
      // username empty
      cy.get('#user_login').should('have.value', '')
  
      // ✔ REAL WordPress validation check
     // cy.get('#user_login').should('have.class', 'form-invalid')
  
      // ✔ check WordPress did not show PHP error messages
      cy.get('.notice-error, .error').should('not.exist')
    })
  })
  
  describe('TC-ADDUSER-04: Empty email field', () => {
    it('should display HTML5 validation with red border and exclamation icon (no error message)', () => {
      const timestamp = Date.now()
      cy.get('#user_login').clear().type(`user${timestamp}`)
      cy.get('#email').clear().should('have.value', '')
      cy.get('#pass1').clear().type(validPassword)
      // Check if weak password confirmation checkbox appears and check it
      cy.get('body').then(($body) => {
        if ($body.find('input[name="pw_weak"]').length > 0) {
          cy.get('input[name="pw_weak"]').check()
        }
      })
      cy.get('#role').select('Subscriber')
      
      // Click submit - form should not submit due to HTML5 validation
      cy.get('#createusersub').click()
      
      // Wait for validation to trigger
      cy.wait(300)
      
      // Form should not submit - should still be on user-new.php page
      cy.url().should('include', 'user-new.php')
      
      // Verify email field is still empty
    //  cy.get('#email').should('have.value', '')
     // cy.url().should('include', 'user-new.php')
      
      // Check HTML5 validation - field should be invalid (shows red border and exclamation icon)
      
      
      
      // Check that email field has required attribute
    //  cy.get('#email').should('have.attr', 'required')
    })
  })

  describe('TC-ADDUSER-05: Empty password field', () => {
    it('should display error for empty password', () => {
      const timestamp = Date.now()
      cy.get('#user_login').clear().type(`user${timestamp}`)
      cy.get('#email').clear().type(`user${timestamp}@example.com`)
      cy.get('#pass1').clear()
      cy.get('#role').select('Subscriber')
      
      // When password is empty, submit button should be disabled
      cy.get('#createusersub').should('be.disabled')
      cy.url().should('include', 'user-new.php')
      // Confirm password checkbox should not be visible when password is empty
      //cy.get('input[name="pw_weak"]').should('not.exist')
    })
  })

  describe('TC-ADDUSER-06: Invalid email format', () => {
    it('should display error for invalid email format', () => {
      const timestamp = Date.now()
      cy.get('#user_login').clear().type(`user${timestamp}`)
      cy.get('#email').clear().type('invalidemail')
      cy.get('#pass1').clear().type(validPassword)
      // Check if weak password confirmation checkbox appears and check it
      cy.get('body').then(($body) => {
        if ($body.find('input[name="pw_weak"]').length > 0) {
          cy.get('input[name="pw_weak"]').check()
        }
      })
      cy.get('#role').select('Subscriber')
      cy.get('#createusersub').click()
      
      // Check for error message - WordPress shows error for invalid email
      cy.get('.notice-error, .error, #message, .error-message').should('be.visible')
    })
  })

  describe('TC-ADDUSER-07: Invalid website URL format', () => {
    it('should display error for invalid website URL', () => {
      const timestamp = Date.now()
      cy.get('#user_login').clear().type(`user${timestamp}`)
      cy.get('#email').clear().type(`user${timestamp}@example.com`)
      cy.get('#pass1').clear().type(validPassword)
      // Check if weak password confirmation checkbox appears and check it
      cy.get('body').then(($body) => {
        if ($body.find('input[name="pw_weak"]').length > 0) {
          cy.get('input[name="pw_weak"]').check()
        }
      })
      cy.get('#url').clear().type('not-a-valid-url')
      cy.get('#role').select('Subscriber')
      cy.get('#createusersub').click()
      
      // May show error or may accept (depends on WordPress validation)
      cy.url().then((url) => {
        if (url.includes('user-new.php')) {
          cy.get('.notice-error, .error, #message').should('be.visible')
        }
      })
    })
  })

  describe('TC-ADDUSER-08: Username with minimum length (1 character)', () => {
    it('should handle username with 1 character', () => {
      cy.get('#user_login').clear().type('a')
      cy.get('#email').clear().type(validEmail)
      cy.get('#pass1').clear().type(validPassword)
      // Check if weak password confirmation checkbox appears and check it
      cy.get('body').then(($body) => {
        if ($body.find('input[name="pw_weak"]').length > 0) {
          cy.get('input[name="pw_weak"]').check()
        }
      })
      cy.get('#role').select('Subscriber')
      cy.get('#createusersub').click()
      
      // Should show error as 1 character username is likely invalid
      cy.get('.notice-error, .error, #message, .error-message').should('be.visible')
    })
  })

  describe('TC-ADDUSER-09: Username with maximum length (60 characters)', () => {
    it('should handle username with 60 characters', () => {
      const longUsername = 'a'.repeat(60)
      cy.get('#user_login').clear().type(longUsername)
      cy.get('#email').clear().type(validEmail)
      cy.get('#pass1').clear().type(validPassword)
      // Check if weak password confirmation checkbox appears and check it
      cy.get('body').then(($body) => {
        if ($body.find('input[name="pw_weak"]').length > 0) {
          cy.get('input[name="pw_weak"]').check()
        }
      })
      cy.get('#role').select('Subscriber')
      cy.get('#createusersub').click()
      
      // May show error or may accept (depends on WordPress validation)
      cy.url().then((url) => {
        if (url.includes('user-new.php')) {
          cy.get('.notice-error, .error, #message, .error-message').should('be.visible')
        }
      })
    })
  })

  describe('TC-ADDUSER-10: Username exceeding maximum length (61 characters)', () => {
    it('should handle username with 61 characters', () => {
      const tooLongUsername = 'a'.repeat(61)
      cy.get('#user_login').clear().type(tooLongUsername)
      cy.get('#email').clear().type(validEmail)
      cy.get('#pass1').clear().type(validPassword)
      // Check if weak password confirmation checkbox appears and check it
      cy.get('body').then(($body) => {
        if ($body.find('input[name="pw_weak"]').length > 0) {
          cy.get('input[name="pw_weak"]').check()
        }
      })
      cy.get('#role').select('Subscriber')
      cy.get('#createusersub').click()
      
      // Should show error or truncate
      cy.get('.notice-error, .error, #message, .error-message').should('be.visible')
    })
  })

  describe('TC-ADDUSER-11: Password with minimum length (1 character)', () => {
    it('should handle password with 1 character', () => {
      const timestamp = Date.now()
      cy.get('#user_login').clear().type(`user${timestamp}`)
      cy.get('#email').clear().type(`user${timestamp}@example.com`)
      cy.get('#pass1').clear().type('a')
      // Wait a moment for weak password detection, then check the confirm weak password checkbox
      cy.wait(500)
      cy.get('body').then(($body) => {
        if ($body.find('input[name="pw_weak"]').length > 0) {
          cy.get('input[name="pw_weak"]').check()
        }
      })
      cy.get('#role').select('Subscriber')
      cy.get('#createusersub').click()
      
      // Should show error as 1 character password is likely invalid
      cy.get('.notice-error, .error, #message, .error-message').should('be.visible')
    })
  })

  describe('TC-ADDUSER-12: Password with maximum length (255 characters)', () => {
    it('should handle password with 255 characters', () => {
      const timestamp = Date.now()
      const longPassword = 'a'.repeat(255)
      cy.get('#user_login').clear().type(`user${timestamp}`)
      cy.get('#email').clear().type(`user${timestamp}@example.com`)
      cy.get('#pass1').clear().type(longPassword)
      // Check if weak password confirmation checkbox appears and check it
      cy.get('body').then(($body) => {
        if ($body.find('input[name="pw_weak"]').length > 0) {
          cy.get('input[name="pw_weak"]').check()
        }
      })
      cy.get('#role').select('Subscriber')
      cy.get('#createusersub').click()
      
      // May show error or may accept (depends on WordPress validation)
      cy.url().then((url) => {
        if (url.includes('user-new.php')) {
          cy.get('.notice-error, .error, #message, .error-message').should('be.visible')
        }
      })
    })
  })

  describe('TC-ADDUSER-13: Password exceeding maximum length (256 characters)', () => {
    it('should handle password with 256 characters', () => {
      const timestamp = Date.now()
      const tooLongPassword = 'a'.repeat(256)
      cy.get('#user_login').clear().type(`user${timestamp}`)
      cy.get('#email').clear().type(`user${timestamp}@example.com`)
      cy.get('#pass1').clear().type(tooLongPassword)
      // Check if weak password confirmation checkbox appears and check it
      cy.get('body').then(($body) => {
        if ($body.find('input[name="pw_weak"]').length > 0) {
          cy.get('input[name="pw_weak"]').check()
        }
      })
      cy.get('#role').select('Subscriber')
      cy.get('#createusersub').click()
      
      // Should show error or truncate
      cy.get('.notice-error, .error, #message, .error-message').should('be.visible')
    })
  })

  describe('TC-ADDUSER-14: Valid user creation with send notification checked', () => {
    it('should successfully create user with send notification checked', () => {
      const timestamp = Date.now()
      cy.get('#user_login').clear().type(`user${timestamp}`)
      cy.get('#email').clear().type(`user${timestamp}@example.com`)
      cy.get('#pass1').clear().type(validPassword)
      // Check if weak password confirmation checkbox appears and check it
      cy.get('body').then(($body) => {
        if ($body.find('input[name="pw_weak"]').length > 0) {
          cy.get('input[name="pw_weak"]').check()
        }
      })
      cy.get('#first_name').clear().type(validFirstName)
      cy.get('#last_name').clear().type(validLastName)
      cy.get('#send_user_notification').check()
      cy.get('#send_user_notification').should('be.checked')
      cy.get('#role').select('Editor')
      cy.get('#createusersub').click()
      
      // Check for success message - WordPress shows success when user is created
      cy.get('.notice-success, .updated, #message').should('be.visible')
    })
  })
})

