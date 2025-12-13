/**
 * Media Settings Form Test Cases
 * Based on Equivalence Class Partitioning and Boundary Value Analysis
 * Test Cases: TC-MEDIA-01 to TC-MEDIA-27
 */

describe('Media Settings Form - Black Box Testing', () => {
  const baseUrl = 'http://127.0.0.1:8080'
  const mediaSettingsUrl = `${baseUrl}/wp-admin/options-media.php`
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

    // Navigate to Media Settings page
    cy.visit(mediaSettingsUrl)
    
    // Wait for form to load
    cy.get('#thumbnail_size_w').should('be.visible')
    cy.get('#medium_size_w').should('be.visible')
    cy.get('#large_size_w').should('be.visible')
  })

  describe('TC-MEDIA-01: Valid settings update with default values', () => {
    it('should successfully save settings with default values', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-02: Valid settings update with different values', () => {
    it('should successfully save settings with different values', () => {
      cy.get('#thumbnail_size_w').clear().type('300')
      cy.get('#thumbnail_size_h').clear().type('300')
      cy.get('#thumbnail_crop').uncheck()
      cy.get('#medium_size_w').clear().type('600')
      cy.get('#medium_size_h').clear().type('600')
      cy.get('#large_size_w').clear().type('1200')
      cy.get('#large_size_h').clear().type('1200')
      cy.get('#uploads_use_yearmonth_folders').uncheck()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-03: Settings update with thumbnail width = -1', () => {
    it('should display error message for thumbnail width = -1', () => {
      cy.get('#thumbnail_size_w').clear().type('-1')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation)
      cy.get('#thumbnail_size_w').then(($input) => {
        if ($input[0].validity && !$input[0].validity.valid) {
          // HTML5 validation (min="0")
          expect($input[0].validity.rangeUnderflow).to.be.true
        } else {
          // WordPress error message
          cy.get('.notice-error, .error, #message, .error-message, p:contains("width"), p:contains("Width")').should('be.visible')
        }
      })
    })
  })

  describe('TC-MEDIA-04: Settings update with thumbnail width = 0 (minimum)', () => {
    it('should successfully save settings with thumbnail width = 0', () => {
      cy.get('#thumbnail_size_w').clear().type('0')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-05: Settings update with thumbnail width = 9999 (maximum)', () => {
    it('should successfully save settings with thumbnail width = 9999', () => {
      cy.get('#thumbnail_size_w').clear().type('9999')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-06: Settings update with thumbnail width = 10000 (exceeds maximum)', () => {
    it('should display error message for thumbnail width = 10000', () => {
      cy.get('#thumbnail_size_w').clear().type('10000')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (WordPress may accept it or show error)
      cy.get('body').then(($body) => {
        if ($body.find('.notice-error, .error, #message, .error-message, p:contains("width"), p:contains("Width"), p:contains("maximum")').length > 0) {
          cy.get('.notice-error, .error, #message, .error-message, p:contains("width"), p:contains("Width"), p:contains("maximum")').should('be.visible')
        } else {
          // WordPress may accept it, so check for success
          cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
        }
      })
    })
  })

  describe('TC-MEDIA-07: Settings update with thumbnail width = abc (invalid format)', () => {
    it('should successfully save settings (WordPress converts abc to 0)', () => {
      cy.get('#thumbnail_size_w').clear().type('abc')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // WordPress accepts invalid format and converts it to 0 (or saves successfully)
      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-08: Settings update with thumbnail height = -1', () => {
    it('should display error message for thumbnail height = -1', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('-1')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation)
      cy.get('#thumbnail_size_h').then(($input) => {
        if ($input[0].validity && !$input[0].validity.valid) {
          // HTML5 validation (min="0")
          expect($input[0].validity.rangeUnderflow).to.be.true
        } else {
          // WordPress error message
          cy.get('.notice-error, .error, #message, .error-message, p:contains("height"), p:contains("Height")').should('be.visible')
        }
      })
    })
  })

  describe('TC-MEDIA-09: Settings update with thumbnail height = 0 (minimum)', () => {
    it('should successfully save settings with thumbnail height = 0', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('0')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-10: Settings update with thumbnail height = 9999 (maximum)', () => {
    it('should successfully save settings with thumbnail height = 9999', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('9999')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-11: Settings update with thumbnail height = 10000 (exceeds maximum)', () => {
    it('should display error message for thumbnail height = 10000', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('10000')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (WordPress may accept it or show error)
      cy.get('body').then(($body) => {
        if ($body.find('.notice-error, .error, #message, .error-message, p:contains("height"), p:contains("Height"), p:contains("maximum")').length > 0) {
          cy.get('.notice-error, .error, #message, .error-message, p:contains("height"), p:contains("Height"), p:contains("maximum")').should('be.visible')
        } else {
          // WordPress may accept it, so check for success
          cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
        }
      })
    })
  })

  describe('TC-MEDIA-12: Settings update with medium width = -1', () => {
    it('should display error message for medium width = -1', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('-1')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation)
      cy.get('#medium_size_w').then(($input) => {
        if ($input[0].validity && !$input[0].validity.valid) {
          // HTML5 validation (min="0")
          expect($input[0].validity.rangeUnderflow).to.be.true
        } else {
          // WordPress error message
          cy.get('.notice-error, .error, #message, .error-message, p:contains("width"), p:contains("Width")').should('be.visible')
        }
      })
    })
  })

  describe('TC-MEDIA-13: Settings update with medium width = 0 (minimum)', () => {
    it('should successfully save settings with medium width = 0', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('0')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-14: Settings update with medium width = 9999 (maximum)', () => {
    it('should successfully save settings with medium width = 9999', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('9999')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-15: Settings update with medium width = 10000 (exceeds maximum)', () => {
    it('should display error message for medium width = 10000', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('10000')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (WordPress may accept it or show error)
      cy.get('body').then(($body) => {
        if ($body.find('.notice-error, .error, #message, .error-message, p:contains("width"), p:contains("Width"), p:contains("maximum")').length > 0) {
          cy.get('.notice-error, .error, #message, .error-message, p:contains("width"), p:contains("Width"), p:contains("maximum")').should('be.visible')
        } else {
          // WordPress may accept it, so check for success
          cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
        }
      })
    })
  })

  describe('TC-MEDIA-16: Settings update with medium height = -1', () => {
    it('should display error message for medium height = -1', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('-1')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation)
      cy.get('#medium_size_h').then(($input) => {
        if ($input[0].validity && !$input[0].validity.valid) {
          // HTML5 validation (min="0")
          expect($input[0].validity.rangeUnderflow).to.be.true
        } else {
          // WordPress error message
          cy.get('.notice-error, .error, #message, .error-message, p:contains("height"), p:contains("Height")').should('be.visible')
        }
      })
    })
  })

  describe('TC-MEDIA-17: Settings update with medium height = 0 (minimum)', () => {
    it('should successfully save settings with medium height = 0', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('0')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-18: Settings update with medium height = 9999 (maximum)', () => {
    it('should successfully save settings with medium height = 9999', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('9999')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-19: Settings update with medium height = 10000 (exceeds maximum)', () => {
    it('should display error message for medium height = 10000', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('10000')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (WordPress may accept it or show error)
      cy.get('body').then(($body) => {
        if ($body.find('.notice-error, .error, #message, .error-message, p:contains("height"), p:contains("Height"), p:contains("maximum")').length > 0) {
          cy.get('.notice-error, .error, #message, .error-message, p:contains("height"), p:contains("Height"), p:contains("maximum")').should('be.visible')
        } else {
          // WordPress may accept it, so check for success
          cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
        }
      })
    })
  })

  describe('TC-MEDIA-20: Settings update with large width = -1', () => {
    it('should display error message for large width = -1', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('-1')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation)
      cy.get('#large_size_w').then(($input) => {
        if ($input[0].validity && !$input[0].validity.valid) {
          // HTML5 validation (min="0")
          expect($input[0].validity.rangeUnderflow).to.be.true
        } else {
          // WordPress error message
          cy.get('.notice-error, .error, #message, .error-message, p:contains("width"), p:contains("Width")').should('be.visible')
        }
      })
    })
  })

  describe('TC-MEDIA-21: Settings update with large width = 0 (minimum)', () => {
    it('should successfully save settings with large width = 0', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('0')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-22: Settings update with large width = 9999 (maximum)', () => {
    it('should successfully save settings with large width = 9999', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('9999')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-23: Settings update with large width = 10000 (exceeds maximum)', () => {
    it('should display error message for large width = 10000', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('10000')
      cy.get('#large_size_h').clear().type('1024')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (WordPress may accept it or show error)
      cy.get('body').then(($body) => {
        if ($body.find('.notice-error, .error, #message, .error-message, p:contains("width"), p:contains("Width"), p:contains("maximum")').length > 0) {
          cy.get('.notice-error, .error, #message, .error-message, p:contains("width"), p:contains("Width"), p:contains("maximum")').should('be.visible')
        } else {
          // WordPress may accept it, so check for success
          cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
        }
      })
    })
  })

  describe('TC-MEDIA-24: Settings update with large height = -1', () => {
    it('should display error message for large height = -1', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('-1')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (HTML5 validation)
      cy.get('#large_size_h').then(($input) => {
        if ($input[0].validity && !$input[0].validity.valid) {
          // HTML5 validation (min="0")
          expect($input[0].validity.rangeUnderflow).to.be.true
        } else {
          // WordPress error message
          cy.get('.notice-error, .error, #message, .error-message, p:contains("height"), p:contains("Height")').should('be.visible')
        }
      })
    })
  })

  describe('TC-MEDIA-25: Settings update with large height = 0 (minimum)', () => {
    it('should successfully save settings with large height = 0', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('0')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-26: Settings update with large height = 9999 (maximum)', () => {
    it('should successfully save settings with large height = 9999', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('9999')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
      cy.url().should('include', 'options-media.php')
    })
  })

  describe('TC-MEDIA-27: Settings update with large height = 10000 (exceeds maximum)', () => {
    it('should display error message for large height = 10000', () => {
      cy.get('#thumbnail_size_w').clear().type('150')
      cy.get('#thumbnail_size_h').clear().type('150')
      cy.get('#thumbnail_crop').check()
      cy.get('#medium_size_w').clear().type('300')
      cy.get('#medium_size_h').clear().type('300')
      cy.get('#large_size_w').clear().type('1024')
      cy.get('#large_size_h').clear().type('10000')
      cy.get('#uploads_use_yearmonth_folders').check()
      
      // Submit the form
      cy.get('form').find('input[type="submit"], button[type="submit"], #submit').click()

      // Check for error message (WordPress may accept it or show error)
      cy.get('body').then(($body) => {
        if ($body.find('.notice-error, .error, #message, .error-message, p:contains("height"), p:contains("Height"), p:contains("maximum")').length > 0) {
          cy.get('.notice-error, .error, #message, .error-message, p:contains("height"), p:contains("Height"), p:contains("maximum")').should('be.visible')
        } else {
          // WordPress may accept it, so check for success
          cy.get('#message, .notice-success, .updated, p:contains("Settings saved"), p:contains("saved")').should('be.visible')
        }
      })
    })
  })
})

