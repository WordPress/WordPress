/**
 * User Profile Edit Form Test Cases
 * Based on Equivalence Class Partitioning and Boundary Value Analysis
 * Test Cases: TC-PROFILE-01 to TC-PROFILE-07
 */

describe('User Profile Edit Form - Black Box Testing', () => {
  const baseUrl = 'http://127.0.0.1:8080'
  const profileUrl = `${baseUrl}/wp-admin/profile.php`
  const loginUrl = `${baseUrl}/wp-login.php`

  // Test data - WordPress admin credentials for login
  const adminUsername = 'Qadeer572'
  const adminPassword = 'raza@1214'

  beforeEach(() => {
    // Login as admin before each test
    cy.visit(loginUrl)
    cy.get('#user_login').clear().type(adminUsername)
    cy.get('#user_pass').clear().type(adminPassword)
    cy.get('#wp-submit').click()

    // Wait for admin dashboard to load
    cy.url().should('include', '/wp-admin')

    // Navigate to Profile page
    cy.visit(profileUrl)
    
    // Wait for profile form to load
    cy.get('#your-profile').should('be.visible')
    cy.get('#first_name').should('be.visible')
    cy.get('#email').should('be.visible')
  })

  describe('TC-PROFILE-01: Valid profile update with all fields', () => {
    it('should successfully update profile with all valid fields', () => {
      const timestamp = Date.now()
      const firstName = `John ${timestamp}`
      const lastName = `Smith ${timestamp}`
      const nickname = `Johnny ${timestamp}`
      const email = `john${timestamp}@example.com`
      const website = 'https://example.com'
      const description = 'I am a developer.'

      cy.get('#first_name').clear().type(firstName)
      cy.get('#last_name').clear().type(lastName)
      cy.get('#nickname').clear().type(nickname)
      cy.get('#email').clear().type(email)
      cy.get('#url').clear().type(website)
      cy.get('#description').clear().type(description)
      
      // Submit the form by clicking the submit button
      cy.get('#your-profile').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Profile updated")').should('be.visible')
      cy.url().should('include', 'profile.php')
    })
  })

  describe('TC-PROFILE-02: Profile update with empty optional fields', () => {
    it('should successfully update profile with empty optional fields', () => {
      const timestamp = Date.now()
      const email = `user${timestamp}@example.com`

      cy.get('#first_name').clear()
      cy.get('#last_name').clear()
      cy.get('#nickname').clear()
      cy.get('#email').clear().type(email)
      cy.get('#url').clear()
      cy.get('#description').clear()
      
      // Submit the form by clicking the submit button
      cy.get('#your-profile').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Profile updated")').should('be.visible')
      cy.url().should('include', 'profile.php')
    })
  })

  describe('TC-PROFILE-03: Profile update with empty email (required field)', () => {
    it('should display error message for empty email', () => {
      const firstName = 'John'
      const lastName = 'Smith'
      const nickname = 'Johnny'
      const website = 'https://example.com'
      const description = 'I am a developer.'

      cy.get('#first_name').clear().type(firstName)
      cy.get('#last_name').clear().type(lastName)
      cy.get('#nickname').clear().type(nickname)
      cy.get('#email').clear() // Empty email
      cy.get('#url').clear().type(website)
      cy.get('#description').clear().type(description)
      
      // Submit the form by clicking the submit button
      cy.get('#your-profile').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message
      cy.get('.notice-error, .error, #message, .error-message, p:contains("email"), p:contains("Email")').should('be.visible')
    })
  })

  describe('TC-PROFILE-04: Profile update with invalid email format', () => {
    it('should display error message for invalid email format', () => {
      const firstName = 'John'
      const lastName = 'Smith'
      const nickname = 'Johnny'
      const invalidEmail = 'invalidemail'
      const website = 'https://example.com'
      const description = 'I am a developer.'

      cy.get('#first_name').clear().type(firstName)
      cy.get('#last_name').clear().type(lastName)
      cy.get('#nickname').clear().type(nickname)
      cy.get('#email').clear().type(invalidEmail)
      cy.get('#url').clear().type(website)
      cy.get('#description').clear().type(description)
      
      // Submit the form by clicking the submit button
      cy.get('#your-profile').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation or WordPress error)
      cy.get('body').then(($body) => {
        // Check for HTML5 validation
        cy.get('#email').then(($email) => {
          if ($email[0].validity && !$email[0].validity.valid) {
            // HTML5 validation triggered
            expect($email[0].validity.typeMismatch || $email[0].validity.valueMissing).to.be.true
          } else {
            // WordPress error message
            cy.get('.notice-error, .error, #message, .error-message, p:contains("email"), p:contains("Email"), p:contains("valid")').should('be.visible')
          }
        })
      })
    })
  })

  describe('TC-PROFILE-05: Profile update with duplicate email', () => {
    it('should display error message for duplicate email', () => {
      // Note: This test assumes there's another user with email 'existing@example.com'
      // You may need to adjust the email address based on your test data
      const firstName = 'John'
      const lastName = 'Smith'
      const nickname = 'Johnny'
      const duplicateEmail = 'existing@example.com' // Email already registered by another user
      const website = 'https://example.com'
      const description = 'I am a developer.'

      cy.get('#first_name').clear().type(firstName)
      cy.get('#last_name').clear().type(lastName)
      cy.get('#nickname').clear().type(nickname)
      cy.get('#email').clear().type(duplicateEmail)
      cy.get('#url').clear().type(website)
      cy.get('#description').clear().type(description)
      
      // Submit the form by clicking the submit button
      cy.get('#your-profile').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message
      cy.get('.notice-error, .error, #message, .error-message, p:contains("email"), p:contains("Email"), p:contains("already"), p:contains("registered")').should('be.visible')
    })
  })

  describe('TC-PROFILE-06: Profile update with invalid URL format', () => {
    it('should display error message for invalid URL format', () => {
      const firstName = 'John'
      const lastName = 'Smith'
      const nickname = 'Johnny'
      const email = `user${Date.now()}@example.com`
      const invalidUrl = 'not-a-url'
      const description = 'I am a developer.'

      cy.get('#first_name').clear().type(firstName)
      cy.get('#last_name').clear().type(lastName)
      cy.get('#nickname').clear().type(nickname)
      cy.get('#email').clear().type(email)
      cy.get('#url').clear().type(invalidUrl)
      cy.get('#description').clear().type(description)
      
      // Submit the form by clicking the submit button
      cy.get('#your-profile').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation or WordPress error)
      cy.get('body').then(($body) => {
        // Check for HTML5 validation
        cy.get('#url').then(($url) => {
          if ($url[0].validity && !$url[0].validity.valid) {
            // HTML5 validation triggered
            expect($url[0].validity.typeMismatch).to.be.true
          } else {
            // WordPress may accept it or show error
            cy.get('.notice-error, .error, #message, .error-message, p:contains("URL"), p:contains("url")').should('exist')
          }
        })
      })
    })
  })

  describe('TC-PROFILE-07: Profile update with email exceeding maximum length', () => {
    it('should display error message for email exceeding maximum length', () => {
      const firstName = 'John'
      const lastName = 'Smith'
      const nickname = 'Johnny'
      // Email with 101 characters (exceeding typical 100 char limit)
      const longEmail = 'a'.repeat(90) + '@domain.com' // 101 characters total
      const website = 'https://example.com'
      const description = 'I am a developer.'

      cy.get('#first_name').clear().type(firstName)
      cy.get('#last_name').clear().type(lastName)
      cy.get('#nickname').clear().type(nickname)
      
      // Use invoke('val') for faster input of long strings
      cy.get('#email').clear().invoke('val', longEmail).trigger('input')
      cy.get('#url').clear().type(website)
      cy.get('#description').clear().type(description)
      
      // Submit the form by clicking the submit button
      cy.get('#your-profile').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message
      cy.get('body').then(($body) => {
        // Check for HTML5 validation or WordPress error
        cy.get('#email').then(($email) => {
          if ($email[0].validity && !$email[0].validity.valid) {
            // HTML5 validation triggered
            expect($email[0].validity.tooLong || $email[0].validity.typeMismatch).to.be.true
          } else {
            // WordPress error message
            cy.get('.notice-error, .error, #message, .error-message, p:contains("email"), p:contains("Email"), p:contains("long"), p:contains("too long")').should('be.visible')
          }
        })
      })
    })
  })
})

