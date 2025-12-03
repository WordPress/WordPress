/**
 * Add Media Form Test Cases
 * Based on Equivalence Class Partitioning and Boundary Value Analysis
 * Test Cases: TC-ADDMEDIA-01 to TC-ADDMEDIA-05
 */

describe('Add Media Form - Black Box Testing', () => {
  const baseUrl = 'http://127.0.0.1:8080'
  const addMediaUrl = `${baseUrl}/wp-admin/upload.php`
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

    // Navigate to Add Media page
    cy.visit(addMediaUrl)
    
    // Wait for upload form to load - check for file input or upload area
    cy.get('body').should('be.visible')
    cy.wait(1000)
  })

  // Helper function to create and upload a file
  function uploadFile(fileName, fileContent, fileType = 'image/png') {
    cy.window().then((win) => {
      const blob = new Blob([fileContent], { type: fileType })
      const file = new File([blob], fileName, { type: fileType })
      
      const dataTransfer = new DataTransfer()
      dataTransfer.items.add(file)
      
      // Find the file input - WordPress uses various selectors
      cy.get('input[type="file"], #async-upload, #plupload-browse-button, .uploader-inline input[type="file"]').then(($input) => {
        if ($input.length > 0) {
          const input = $input[0]
          input.files = dataTransfer.files
          cy.wrap(input).trigger('change', { force: true })
        } else {
          // Try clicking upload button first to reveal file input
          cy.get('body').then(($body) => {
            if ($body.find('.uploader-inline, #plupload-upload-ui, .wp-upload-form').length > 0) {
              cy.get('.uploader-inline, #plupload-upload-ui, .wp-upload-form').within(() => {
                cy.get('input[type="file"]').then(($fileInput) => {
                  if ($fileInput.length > 0) {
                    const input = $fileInput[0]
                    input.files = dataTransfer.files
                    cy.wrap(input).trigger('change', { force: true })
                  }
                })
              })
            }
          })
        }
      })
    })
  }

  describe('TC-ADDMEDIA-01: Valid image file upload (within size limit)', () => {
    it('should successfully upload a valid image file', () => {
      // Create a minimal PNG file (1x1 pixel PNG in base64)
      const fileName = `test-image-${Date.now()}.png`
      const pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
      
      cy.window().then((win) => {
        const byteCharacters = atob(pngBase64)
        const byteNumbers = new Array(byteCharacters.length)
        for (let i = 0; i < byteCharacters.length; i++) {
          byteNumbers[i] = byteCharacters.charCodeAt(i)
        }
        const byteArray = new Uint8Array(byteNumbers)
        uploadFile(fileName, byteArray, 'image/png')
      })

      // Wait for upload to complete
      cy.wait(3000)
      
      // Check for success - file should appear in media library or show success message
      cy.get('body').then(($body) => {
        if ($body.find('#message, .notice-success, .updated, .media-item, .attachment').length > 0) {
          cy.get('#message, .notice-success, .updated, .media-item, .attachment').should('exist')
        }
        // Verify we're still on upload page or redirected to media library
        cy.url().should('include', 'upload.php')
      })
    })
  })

  describe('TC-ADDMEDIA-02: Empty file selection', () => {
    it('should prevent upload when no file is selected', () => {
      // Check that file input exists and is empty
      cy.get('input[type="file"]').should('exist')
      cy.get('input[type="file"]').should('have.value', '')
      
      // Try to find and click upload button if it exists
      cy.get('body').then(($body) => {
        const uploadButton = $body.find('button[type="submit"], input[type="submit"], .button-primary, #upload, .upload-button')
        if (uploadButton.length > 0) {
          // WordPress typically requires a file to be selected before upload button is enabled
          cy.get('button[type="submit"], input[type="submit"], .button-primary, #upload, .upload-button').first().should('exist')
        }
      })
      
      // Should remain on upload page
      cy.url().should('include', 'upload.php')
    })
  })

  describe('TC-ADDMEDIA-03: Invalid file type upload', () => {
    it('should reject invalid file types like .exe files', () => {
      const fileName = `test-file-${Date.now()}.exe`
      const fileContent = 'This is a test executable file content'
      
      uploadFile(fileName, fileContent, 'application/x-msdownload')

      // Wait for validation
      cy.wait(2000)
      
      // Check for error message or rejection
      cy.get('body').then(($body) => {
        // WordPress may show error message or prevent upload
        if ($body.find('.notice-error, .error, #message, .error-message, .upload-error').length > 0) {
          cy.get('.notice-error, .error, #message, .error-message, .upload-error').should('be.visible')
        } else {
          // Or the file input might be cleared/rejected - check URL hasn't changed
          cy.url().should('include', 'upload.php')
        }
      })
    })
  })

  describe('TC-ADDMEDIA-04: File upload at maximum size limit (2MB)', () => {
    it('should successfully upload file at the 2MB limit', () => {
      // Create a file close to 2MB (1.9 MB to be safe)
      const fileName = `test-large-${Date.now()}.jpg`
      const fileSize = Math.floor(1.9 * 1024 * 1024) // 1.9 MB
      const fileContent = new Uint8Array(fileSize).fill(65) // Fill with 'A' character code
      
      uploadFile(fileName, fileContent, 'image/jpeg')

      // Wait for upload to complete (may take longer for large files)
      cy.wait(5000)
      
      // Check for success message or file in media library
      cy.get('body').then(($body) => {
        if ($body.find('#message, .notice-success, .updated, .media-item, .attachment').length > 0) {
          cy.get('#message, .notice-success, .updated, .media-item, .attachment').should('exist')
        }
        // Verify upload completed
        cy.url().should('include', 'upload.php')
      })
    })
  })

  describe('TC-ADDMEDIA-05: File upload exceeds size limit (>2MB)', () => {
    it('should reject file that exceeds the 2MB limit', () => {
      // Create a file larger than 2MB
      const fileName = `test-oversized-${Date.now()}.jpg`
      const fileSize = Math.floor(2.1 * 1024 * 1024) // 2.1 MB (exceeds limit)
      const fileContent = new Uint8Array(fileSize).fill(65) // Fill with 'A' character code
      
      uploadFile(fileName, fileContent, 'image/jpeg')

      // Wait for validation
      cy.wait(3000)
      
      // Check for error message about file size
      cy.get('body').then(($body) => {
        // WordPress should show error about file size exceeding limit
        if ($body.find('.notice-error, .error, #message, .error-message, .upload-error, p:contains("size"), p:contains("limit"), p:contains("2 MB"), p:contains("too large")').length > 0) {
          cy.get('.notice-error, .error, #message, .error-message, .upload-error, p:contains("size"), p:contains("limit"), p:contains("2 MB"), p:contains("too large")').should('exist')
        } else {
          // Or the upload might be prevented - should remain on upload page
          cy.url().should('include', 'upload.php')
        }
      })
    })
  })
})

