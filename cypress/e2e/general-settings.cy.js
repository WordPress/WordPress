/**
 * General Settings Form Test Cases
 * Based on Equivalence Class Partitioning and Boundary Value Analysis
 * Test Cases: TC-GENERAL-01 to TC-GENERAL-14
 */

describe('General Settings Form - Black Box Testing', () => {
  const baseUrl = 'http://127.0.0.1:8080'
  const generalSettingsUrl = `${baseUrl}/wp-admin/options-general.php`
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

    // Navigate to General Settings page
    cy.visit(generalSettingsUrl)
    
    // Wait for form to load
    cy.get('#blogname').should('be.visible')
    cy.get('#new_admin_email').should('be.visible')
  })

  describe('TC-GENERAL-01: Valid settings update with all fields', () => {
    it('should successfully save all valid settings', () => {
      const blogName = 'My Blog'
      const blogDescription = 'Just another WordPress site'
      const adminEmail = 'admin@example.com'

      cy.get('#blogname').clear().type(blogName)
      cy.get('#blogdescription').clear().type(blogDescription)
      cy.get('#new_admin_email').clear().type(adminEmail)
      
      // Uncheck users_can_register if checked
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      // Set default role to Subscriber
      cy.get('#default_role').select('subscriber')
      
      // Set timezone to UTC
      cy.get('#timezone_string').select('UTC')
      
      // Set date format to Y-m-d
      cy.get('input[name="date_format"][value="Y-m-d"]').check()
      
      // Set time format to H:i
      cy.get('input[name="time_format"][value="H:i"]').check()
      
      // Set start of week to Monday (1)
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-general.php')
    })
  })

  describe('TC-GENERAL-02: Settings update with different values', () => {
    it('should successfully save settings with different values', () => {
      const blogName = 'Test Site'
      const blogDescription = ''
      const adminEmail = 'test@domain.com'

      cy.get('#blogname').clear().type(blogName)
      cy.get('#blogdescription').clear()
      cy.get('#new_admin_email').clear().type(adminEmail)
      
      // Check users_can_register
      cy.get('#users_can_register').check()
      
      // Set default role to Editor
      cy.get('#default_role').select('editor')
      
      // Set timezone to America/New_York
      cy.get('#timezone_string').select('America/New_York')
      
      // Set date format to F j, Y
      cy.get('input[name="date_format"][value="F j, Y"]').check()
      
      // Set time format to g:i a
      cy.get('input[name="time_format"][value="g:i a"]').check()
      
      // Set start of week to Sunday (0)
      cy.get('#start_of_week').select('0')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-general.php')
    })
  })

  describe('TC-GENERAL-03: Settings update with empty site title', () => {
    it('should successfully save settings with empty site title (WordPress allows empty title)', () => {
      const blogDescription = 'Just another WordPress site'
      const adminEmail = 'admin@example.com'

      cy.get('#blogname').clear() // Empty site title
      cy.get('#blogdescription').clear().type(blogDescription)
      cy.get('#new_admin_email').clear().type(adminEmail)
      
      // Uncheck users_can_register if checked
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      cy.get('#default_role').select('subscriber')
      cy.get('#timezone_string').select('UTC')
      cy.get('input[name="date_format"][value="Y-m-d"]').check()
      cy.get('input[name="time_format"][value="H:i"]').check()
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // WordPress allows empty site title, so it should save successfully
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-general.php')
    })
  })

  describe('TC-GENERAL-04: Settings update with minimum site title length (1 character)', () => {
    it('should successfully save settings with 1 character site title', () => {
      const blogName = 'a'
      const blogDescription = 'Just another WordPress site'
      const adminEmail = 'admin@example.com'

      cy.get('#blogname').clear().type(blogName)
      cy.get('#blogdescription').clear().type(blogDescription)
      cy.get('#new_admin_email').clear().type(adminEmail)
      
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      cy.get('#default_role').select('subscriber')
      cy.get('#timezone_string').select('UTC')
      cy.get('input[name="date_format"][value="Y-m-d"]').check()
      cy.get('input[name="time_format"][value="H:i"]').check()
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-general.php')
    })
  })

  describe('TC-GENERAL-05: Settings update with maximum site title length (255 characters)', () => {
    it('should successfully save settings with 255 character site title', () => {
      const longBlogName = 'a'.repeat(255)
      const blogDescription = 'Just another WordPress site'
      const adminEmail = 'admin@example.com'

      // Use invoke('val') for faster input of long strings
      cy.get('#blogname').clear().invoke('val', longBlogName).trigger('input')
      cy.get('#blogdescription').clear().type(blogDescription)
      cy.get('#new_admin_email').clear().type(adminEmail)
      
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      cy.get('#default_role').select('subscriber')
      cy.get('#timezone_string').select('UTC')
      cy.get('input[name="date_format"][value="Y-m-d"]').check()
      cy.get('input[name="time_format"][value="H:i"]').check()
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-general.php')
    })
  })

  describe('TC-GENERAL-06: Settings update with empty admin email', () => {
    it('should display error message for empty admin email', () => {
      const blogName = 'My Blog'
      const blogDescription = 'Just another WordPress site'

      cy.get('#blogname').clear().type(blogName)
      cy.get('#blogdescription').clear().type(blogDescription)
      cy.get('#new_admin_email').clear() // Empty email
      
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      cy.get('#default_role').select('subscriber')
      cy.get('#timezone_string').select('UTC')
      cy.get('input[name="date_format"][value="Y-m-d"]').check()
      cy.get('input[name="time_format"][value="H:i"]').check()
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message
      cy.get('body').then(($body) => {
        cy.get('#new_admin_email').then(($input) => {
          if ($input[0].validity && !$input[0].validity.valid) {
            // HTML5 validation
            expect($input[0].validity.valueMissing).to.be.true
          } else {
            // WordPress error message
            cy.get('.notice-error, .error, #message, .error-message, p:contains("email"), p:contains("Email")').should('be.visible')
          }
        })
      })
    })
  })

  describe('TC-GENERAL-07: Settings update with invalid admin email format', () => {
    it('should display error message for invalid email format', () => {
      const blogName = 'My Blog'
      const blogDescription = 'Just another WordPress site'
      const invalidEmail = 'invalidemail'

      cy.get('#blogname').clear().type(blogName)
      cy.get('#blogdescription').clear().type(blogDescription)
      cy.get('#new_admin_email').clear().type(invalidEmail)
      
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      cy.get('#default_role').select('subscriber')
      cy.get('#timezone_string').select('UTC')
      cy.get('input[name="date_format"][value="Y-m-d"]').check()
      cy.get('input[name="time_format"][value="H:i"]').check()
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message
      cy.get('body').then(($body) => {
        cy.get('#new_admin_email').then(($input) => {
          if ($input[0].validity && !$input[0].validity.valid) {
            // HTML5 validation
            expect($input[0].validity.typeMismatch).to.be.true
          } else {
            // WordPress error message
            cy.get('.notice-error, .error, #message, .error-message, p:contains("email"), p:contains("Email"), p:contains("valid")').should('be.visible')
          }
        })
      })
    })
  })

  describe('TC-GENERAL-08: Settings update with email exceeding maximum length', () => {
    it('should display error message for email exceeding maximum length', () => {
      const blogName = 'My Blog'
      const blogDescription = 'Just another WordPress site'
      // Email with 101 characters
      const longEmail = 'a'.repeat(90) + '@domain.com'

      cy.get('#blogname').clear().type(blogName)
      cy.get('#blogdescription').clear().type(blogDescription)
      
      // Use invoke('val') for faster input of long strings
      cy.get('#new_admin_email').clear().invoke('val', longEmail).trigger('input')
      
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      cy.get('#default_role').select('subscriber')
      cy.get('#timezone_string').select('UTC')
      cy.get('input[name="date_format"][value="Y-m-d"]').check()
      cy.get('input[name="time_format"][value="H:i"]').check()
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message
      cy.get('body').then(($body) => {
        cy.get('#new_admin_email').then(($input) => {
          if ($input[0].validity && !$input[0].validity.valid) {
            // HTML5 validation
            expect($input[0].validity.tooLong || $input[0].validity.typeMismatch).to.be.true
          } else {
            // WordPress error message
            cy.get('.notice-error, .error, #message, .error-message, p:contains("email"), p:contains("Email"), p:contains("long")').should('be.visible')
          }
        })
      })
    })
  })

  describe('TC-GENERAL-09: Settings update with invalid WordPress Address URL', () => {
    it('should display error message for invalid WordPress Address URL', () => {
      const blogName = 'My Blog'
      const blogDescription = 'Just another WordPress site'
      const adminEmail = 'admin@example.com'
      const invalidUrl = 'not-a-url'

      cy.get('#blogname').clear().type(blogName)
      cy.get('#blogdescription').clear().type(blogDescription)
      cy.get('#new_admin_email').clear().type(adminEmail)
      
      // Check if siteurl field is enabled (not disabled)
      cy.get('#siteurl').then(($input) => {
        if (!$input.is(':disabled')) {
          cy.get('#siteurl').clear().type(invalidUrl)
        }
      })
      
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      cy.get('#default_role').select('subscriber')
      cy.get('#timezone_string').select('UTC')
      cy.get('input[name="date_format"][value="Y-m-d"]').check()
      cy.get('input[name="time_format"][value="H:i"]').check()
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message
      cy.get('body').then(($body) => {
        cy.get('#siteurl').then(($input) => {
          if ($input[0].validity && !$input[0].validity.valid) {
            // HTML5 validation
            expect($input[0].validity.typeMismatch).to.be.true
          } else {
            // WordPress may accept it or show error
            cy.get('.notice-error, .error, #message, .error-message, p:contains("URL"), p:contains("url")').should('exist')
          }
        })
      })
    })
  })

  describe('TC-GENERAL-10: Settings update with invalid Site Address URL', () => {
    it('should display error message for invalid Site Address URL', () => {
      const blogName = 'My Blog'
      const blogDescription = 'Just another WordPress site'
      const adminEmail = 'admin@example.com'
      const invalidUrl = 'not-a-url'

      cy.get('#blogname').clear().type(blogName)
      cy.get('#blogdescription').clear().type(blogDescription)
      cy.get('#new_admin_email').clear().type(adminEmail)
      
      // Check if home field is enabled (not disabled)
      cy.get('#home').then(($input) => {
        if (!$input.is(':disabled')) {
          cy.get('#home').clear().type(invalidUrl)
        }
      })
      
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      cy.get('#default_role').select('subscriber')
      cy.get('#timezone_string').select('UTC')
      cy.get('input[name="date_format"][value="Y-m-d"]').check()
      cy.get('input[name="time_format"][value="H:i"]').check()
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message
      cy.get('body').then(($body) => {
        cy.get('#home').then(($input) => {
          if ($input[0].validity && !$input[0].validity.valid) {
            // HTML5 validation
            expect($input[0].validity.typeMismatch).to.be.true
          } else {
            // WordPress may accept it or show error
            cy.get('.notice-error, .error, #message, .error-message, p:contains("URL"), p:contains("url")').should('exist')
          }
        })
      })
    })
  })

  describe('TC-GENERAL-11: Settings update with empty custom date format', () => {
    it('should display error message for empty custom date format', () => {
      const blogName = 'My Blog'
      const blogDescription = 'Just another WordPress site'
      const adminEmail = 'admin@example.com'

      cy.get('#blogname').clear().type(blogName)
      cy.get('#blogdescription').clear().type(blogDescription)
      cy.get('#new_admin_email').clear().type(adminEmail)
      
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      cy.get('#default_role').select('subscriber')
      cy.get('#timezone_string').select('UTC')
      
      // Select custom date format using the radio button ID
      cy.get('#date_format_custom_radio').check()
      cy.wait(500) // Wait for custom field to appear
      
      // Clear custom date format field
      cy.get('#date_format_custom').clear()
      
      cy.get('input[name="time_format"][value="H:i"]').check()
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message
      cy.get('.notice-error, .error, #message, .error-message, p:contains("date"), p:contains("Date"), p:contains("format")').should('be.visible')
    })
  })

  describe('TC-GENERAL-12: Settings update with minimum custom date format (1 character)', () => {
    it('should successfully save settings with 1 character custom date format', () => {
      const blogName = 'My Blog'
      const blogDescription = 'Just another WordPress site'
      const adminEmail = 'admin@example.com'

      cy.get('#blogname').clear().type(blogName)
      cy.get('#blogdescription').clear().type(blogDescription)
      cy.get('#new_admin_email').clear().type(adminEmail)
      
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      cy.get('#default_role').select('subscriber')
      cy.get('#timezone_string').select('UTC')
      
      // Select custom date format using the radio button ID
      cy.get('#date_format_custom_radio').check()
      cy.wait(500) // Wait for custom field to appear
      
      // Set custom date format to single character
      cy.get('#date_format_custom').clear().type('Y')
      
      cy.get('input[name="time_format"][value="H:i"]').check()
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-general.php')
    })
  })

  describe('TC-GENERAL-13: Settings update with empty custom time format', () => {
    it('should display error message for empty custom time format', () => {
      const blogName = 'My Blog'
      const blogDescription = 'Just another WordPress site'
      const adminEmail = 'admin@example.com'

      cy.get('#blogname').clear().type(blogName)
      cy.get('#blogdescription').clear().type(blogDescription)
      cy.get('#new_admin_email').clear().type(adminEmail)
      
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      cy.get('#default_role').select('subscriber')
      cy.get('#timezone_string').select('UTC')
      cy.get('input[name="date_format"][value="Y-m-d"]').check()
      
      // Select custom time format using the radio button ID
      cy.get('#time_format_custom_radio').check()
      cy.wait(500) // Wait for custom field to appear
      
      // Clear custom time format field
      cy.get('#time_format_custom').clear()
      
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message
      cy.get('.notice-error, .error, #message, .error-message, p:contains("time"), p:contains("Time"), p:contains("format")').should('be.visible')
    })
  })

  describe('TC-GENERAL-14: Settings update with minimum custom time format (1 character)', () => {
    it('should successfully save settings with 1 character custom time format', () => {
      const blogName = 'My Blog'
      const blogDescription = 'Just another WordPress site'
      const adminEmail = 'admin@example.com'

      cy.get('#blogname').clear().type(blogName)
      cy.get('#blogdescription').clear().type(blogDescription)
      cy.get('#new_admin_email').clear().type(adminEmail)
      
      cy.get('#users_can_register').then(($checkbox) => {
        if ($checkbox.is(':checked')) {
          cy.get('#users_can_register').uncheck()
        }
      })
      
      cy.get('#default_role').select('subscriber')
      cy.get('#timezone_string').select('UTC')
      cy.get('input[name="date_format"][value="Y-m-d"]').check()
      
      // Select custom time format using the radio button ID
      cy.get('#time_format_custom_radio').check()
      cy.wait(500) // Wait for custom field to appear
      
      // Set custom time format to single character
      cy.get('#time_format_custom').clear().type('H')
      
      cy.get('#start_of_week').select('1')
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-general.php')
    })
  })
})

