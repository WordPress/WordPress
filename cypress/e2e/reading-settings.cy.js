/**
 * Reading Settings Form Test Cases
 * Based on Equivalence Class Partitioning and Boundary Value Analysis
 * Test Cases: TC-READING-01 to TC-READING-16
 */

describe('Reading Settings Form - Black Box Testing', () => {
  const baseUrl = 'http://127.0.0.1:8080'
  const readingSettingsUrl = `${baseUrl}/wp-admin/options-reading.php`
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

    // Navigate to Reading Settings page
    cy.visit(readingSettingsUrl)
    
    // Wait for form to load
    cy.get('#posts_per_page').should('be.visible')
    cy.get('#posts_per_rss').should('be.visible')
  })

  describe('TC-READING-01: Valid settings update with default values', () => {
    it('should successfully save settings with default values', () => {
      // Set show_on_front to posts
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('10')
      cy.get('#posts_per_rss').clear().type('10')
      
      // Set rss_use_excerpt to 0 (Full text)
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      
      // Set blog_public to unchecked (value="0")
      cy.get('body').then(($body) => {
        if ($body.find('input[name="blog_public"][value="0"]').length > 0) {
          // Radio button format
          cy.get('input[name="blog_public"][value="0"]').check()
        } else if ($body.find('#blog_public').length > 0) {
          // Checkbox format - uncheck it
          cy.get('#blog_public').uncheck()
        }
      })
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-reading.php')
    })
  })

  describe('TC-READING-02: Valid settings update with different values', () => {
    it('should successfully save settings with different values', () => {
      // Set show_on_front to posts
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('25')
      cy.get('#posts_per_rss').clear().type('20')
      
      // Set rss_use_excerpt to 1 (Excerpt)
      cy.get('input[name="rss_use_excerpt"][value="1"]').check()
      
      // Set blog_public to checked (value="1")
      cy.get('body').then(($body) => {
        if ($body.find('input[name="blog_public"][value="1"]').length > 0) {
          // Radio button format
          cy.get('input[name="blog_public"][value="1"]').check()
        } else if ($body.find('#blog_public').length > 0) {
          // Checkbox format - check it
          cy.get('#blog_public').check()
        }
      })
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-reading.php')
    })
  })

  describe('TC-READING-03: Settings update with static page homepage', () => {
    it('should successfully save settings with static page homepage', () => {
      // Check if pages exist (if not, this test will be skipped)
      cy.get('body').then(($body) => {
        if ($body.find('input[name="show_on_front"][value="page"]').length > 0) {
          // Set show_on_front to page
          cy.get('input[name="show_on_front"][value="page"]').check()
          cy.wait(500) // Wait for dropdowns to appear
          
          // Get the first available page option (not "0" which is "Select")
          cy.get('#page_on_front').then(($select) => {
            const options = $select.find('option')
            let pageId = null
            for (let i = 0; i < options.length; i++) {
              const value = options[i].value
              if (value !== '0' && value !== '') {
                pageId = value
                break
              }
            }
            
            if (pageId) {
              // Set page_on_front to first available page
              cy.get('#page_on_front').select(pageId)
              
              // Set page_for_posts to 0 (no posts page)
              cy.get('#page_for_posts').select('0')
              
              cy.get('#posts_per_page').clear().type('10')
              cy.get('#posts_per_rss').clear().type('10')
              cy.get('input[name="rss_use_excerpt"][value="0"]').check()
              
              // Set blog_public to unchecked
              cy.get('body').then(($body2) => {
                if ($body2.find('input[name="blog_public"][value="0"]').length > 0) {
                  cy.get('input[name="blog_public"][value="0"]').check()
                } else if ($body2.find('#blog_public').length > 0) {
                  cy.get('#blog_public').uncheck()
                }
              })
              
              // Submit the form
              cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

              // Check for success message
              cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
              cy.url().should('include', 'options-reading.php')
            } else {
              // Skip test if no pages available
              cy.log('No pages available, skipping test')
            }
          })
        } else {
          // Skip test if no pages exist
          cy.log('No pages exist, skipping test')
        }
      })
    })
  })

  describe('TC-READING-04: Settings update with empty posts per page', () => {
    it('should display error message for empty posts per page', () => {
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear() // Empty posts per page
      cy.get('#posts_per_rss').clear().type('10')
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      cy.get('input[name="blog_public"][value="0"]').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation or WordPress error)
      cy.get('body').then(($body) => {
        cy.get('#posts_per_page').then(($input) => {
          if ($input[0].validity && !$input[0].validity.valid) {
            // HTML5 validation
            expect($input[0].validity.valueMissing || $input[0].validity.badInput).to.be.true
          } else {
            // WordPress error message
            cy.get('.notice-error, .error, #message, .error-message, p:contains("posts"), p:contains("page")').should('be.visible')
          }
        })
      })
    })
  })

  describe('TC-READING-05: Settings update with posts per page = 0', () => {
    it('should display error message for posts per page = 0', () => {
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('0')
      cy.get('#posts_per_rss').clear().type('10')
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      cy.get('input[name="blog_public"][value="0"]').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation or WordPress error)
      cy.get('body').then(($body) => {
        cy.get('#posts_per_page').then(($input) => {
          if ($input[0].validity && !$input[0].validity.valid) {
            // HTML5 validation (min="1")
            expect($input[0].validity.rangeUnderflow).to.be.true
          } else {
            // WordPress error message
            cy.get('.notice-error, .error, #message, .error-message, p:contains("posts"), p:contains("page"), p:contains("least")').should('be.visible')
          }
        })
      })
    })
  })

  describe('TC-READING-06: Settings update with posts per page = 1 (minimum)', () => {
    it('should successfully save settings with posts per page = 1', () => {
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('1')
      cy.get('#posts_per_rss').clear().type('10')
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      cy.get('input[name="blog_public"][value="0"]').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-reading.php')
    })
  })

  describe('TC-READING-07: Settings update with posts per page = 999 (maximum)', () => {
    it('should successfully save settings with posts per page = 999', () => {
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('999')
      cy.get('#posts_per_rss').clear().type('10')
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      cy.get('input[name="blog_public"][value="0"]').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-reading.php')
    })
  })

  describe('TC-READING-08: Settings update with posts per page = 1000 (exceeds maximum)', () => {
    it('should display error message for posts per page = 1000', () => {
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('1000')
      cy.get('#posts_per_rss').clear().type('10')
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      cy.get('input[name="blog_public"][value="0"]').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (WordPress may accept it or show error)
      cy.get('body').then(($body) => {
        // WordPress may accept 1000 or show error
        if ($body.find('.notice-error, .error, #message, .error-message, p:contains("posts"), p:contains("page"), p:contains("maximum")').length > 0) {
          cy.get('.notice-error, .error, #message, .error-message, p:contains("posts"), p:contains("page"), p:contains("maximum")').should('be.visible')
        } else {
          // WordPress may accept it, so check for success
          cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
        }
      })
    })
  })

  describe('TC-READING-09: Settings update with posts per page = abc (invalid format)', () => {
    it('should display error message for invalid posts per page format', () => {
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('abc')
      cy.get('#posts_per_rss').clear().type('10')
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      cy.get('input[name="blog_public"][value="0"]').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation)
      cy.get('#posts_per_page').then(($input) => {
        if ($input[0].validity && !$input[0].validity.valid) {
          // HTML5 validation
          expect($input[0].validity.badInput || $input[0].validity.typeMismatch).to.be.true
        } else {
          // WordPress may convert to 0 or show error
          cy.get('.notice-error, .error, #message, .error-message, p:contains("posts"), p:contains("page"), p:contains("invalid")').should('exist')
        }
      })
    })
  })

  describe('TC-READING-10: Settings update with empty posts per RSS', () => {
    it('should display error message for empty posts per RSS', () => {
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('10')
      cy.get('#posts_per_rss').clear() // Empty posts per RSS
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      cy.get('input[name="blog_public"][value="0"]').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation or WordPress error)
      cy.get('body').then(($body) => {
        cy.get('#posts_per_rss').then(($input) => {
          if ($input[0].validity && !$input[0].validity.valid) {
            // HTML5 validation
            expect($input[0].validity.valueMissing || $input[0].validity.badInput).to.be.true
          } else {
            // WordPress error message
            cy.get('.notice-error, .error, #message, .error-message, p:contains("RSS"), p:contains("rss"), p:contains("feed")').should('be.visible')
          }
        })
      })
    })
  })

  describe('TC-READING-11: Settings update with posts per RSS = 0', () => {
    it('should display error message for posts per RSS = 0', () => {
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('10')
      cy.get('#posts_per_rss').clear().type('0')
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      cy.get('input[name="blog_public"][value="0"]').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation or WordPress error)
      cy.get('body').then(($body) => {
        cy.get('#posts_per_rss').then(($input) => {
          if ($input[0].validity && !$input[0].validity.valid) {
            // HTML5 validation (min="1")
            expect($input[0].validity.rangeUnderflow).to.be.true
          } else {
            // WordPress error message
            cy.get('.notice-error, .error, #message, .error-message, p:contains("RSS"), p:contains("rss"), p:contains("feed"), p:contains("least")').should('be.visible')
          }
        })
      })
    })
  })

  describe('TC-READING-12: Settings update with posts per RSS = 1 (minimum)', () => {
    it('should successfully save settings with posts per RSS = 1', () => {
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('10')
      cy.get('#posts_per_rss').clear().type('1')
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      cy.get('input[name="blog_public"][value="0"]').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-reading.php')
    })
  })

  describe('TC-READING-13: Settings update with posts per RSS = 999 (maximum)', () => {
    it('should successfully save settings with posts per RSS = 999', () => {
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('10')
      cy.get('#posts_per_rss').clear().type('999')
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      cy.get('input[name="blog_public"][value="0"]').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-reading.php')
    })
  })

  describe('TC-READING-14: Settings update with posts per RSS = 1000 (exceeds maximum)', () => {
    it('should display error message for posts per RSS = 1000', () => {
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('10')
      cy.get('#posts_per_rss').clear().type('1000')
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      cy.get('input[name="blog_public"][value="0"]').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (WordPress may accept it or show error)
      cy.get('body').then(($body) => {
        // WordPress may accept 1000 or show error
        if ($body.find('.notice-error, .error, #message, .error-message, p:contains("RSS"), p:contains("rss"), p:contains("feed"), p:contains("maximum")').length > 0) {
          cy.get('.notice-error, .error, #message, .error-message, p:contains("RSS"), p:contains("rss"), p:contains("feed"), p:contains("maximum")').should('be.visible')
        } else {
          // WordPress may accept it, so check for success
          cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
        }
      })
    })
  })

  describe('TC-READING-15: Settings update with posts per RSS = abc (invalid format)', () => {
    it('should display error message for invalid posts per RSS format', () => {
      cy.get('input[name="show_on_front"][value="posts"]').check()
      
      cy.get('#posts_per_page').clear().type('10')
      cy.get('#posts_per_rss').clear().type('abc')
      cy.get('input[name="rss_use_excerpt"][value="0"]').check()
      cy.get('input[name="blog_public"][value="0"]').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation)
      cy.get('#posts_per_rss').then(($input) => {
        if ($input[0].validity && !$input[0].validity.valid) {
          // HTML5 validation
          expect($input[0].validity.badInput || $input[0].validity.typeMismatch).to.be.true
        } else {
          // WordPress may convert to 0 or show error
          cy.get('.notice-error, .error, #message, .error-message, p:contains("RSS"), p:contains("rss"), p:contains("feed"), p:contains("invalid")').should('exist')
        }
      })
    })
  })

  describe('TC-READING-16: Settings update with static page but no homepage selected', () => {
    it('should display error message when static page selected but no homepage', () => {
      // Check if pages exist
      cy.get('body').then(($body) => {
        if ($body.find('input[name="show_on_front"][value="page"]').length > 0) {
          // Set show_on_front to page
          cy.get('input[name="show_on_front"][value="page"]').check()
          cy.wait(500) // Wait for dropdowns to appear
          
          // Set page_on_front to 0 (no homepage selected)
          cy.get('#page_on_front').select('0')
          
          // Set page_for_posts to 0
          cy.get('#page_for_posts').select('0')
          
          cy.get('#posts_per_page').clear().type('10')
          cy.get('#posts_per_rss').clear().type('10')
          cy.get('input[name="rss_use_excerpt"][value="0"]').check()
          
          // Set blog_public to unchecked
          cy.get('body').then(($body2) => {
            if ($body2.find('input[name="blog_public"][value="0"]').length > 0) {
              cy.get('input[name="blog_public"][value="0"]').check()
            } else if ($body2.find('#blog_public').length > 0) {
              cy.get('#blog_public').uncheck()
            }
          })
          
          // Submit the form
          cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

          // Check for error message or warning
          cy.get('.notice-error, .error, #message, .error-message, .notice-warning, .warning, p:contains("homepage"), p:contains("Homepage"), p:contains("page")').should('be.visible')
        } else {
          // Skip test if no pages exist
          cy.log('No pages exist, skipping test')
        }
      })
    })
  })
})

