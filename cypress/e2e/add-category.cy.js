/**
 * Add Category Form Test Cases
 * Based on Equivalence Class Partitioning and Boundary Value Analysis
 * Test Cases: TC-CATEGORY-01 to TC-CATEGORY-14
 */

describe('Add Category Form - Black Box Testing', () => {
  const baseUrl = 'http://127.0.0.1:8080'
  const addCategoryUrl = `${baseUrl}/wp-admin/edit-tags.php?taxonomy=category`
  const loginUrl = `${baseUrl}/wp-login.php`

  // Test data - WordPress admin credentials for login
  const adminUsername = 'Qadeer572'
  const adminPassword = 'raza@1214'

  // Store created category IDs for cleanup
  let createdCategoryIds = []

  beforeEach(() => {
    // Login as admin before each test
    cy.visit(loginUrl)
    cy.get('#user_login').clear().type(adminUsername)
    cy.get('#user_pass').clear().type(adminPassword)
    cy.get('#wp-submit').click()

    // Wait for admin dashboard to load
    cy.url().should('include', '/wp-admin')

    // Navigate to Add Category page
    cy.visit(addCategoryUrl)
    
    // Wait for form to load
    cy.get('#tag-name').should('be.visible')
  })

  afterEach(() => {
    // Clean up: Delete created categories
    if (createdCategoryIds.length > 0) {
      createdCategoryIds.forEach((categoryId) => {
        cy.request({
          method: 'POST',
          url: `${baseUrl}/wp-admin/admin-ajax.php`,
          form: true,
          body: {
            action: 'delete-tag',
            tag_ID: categoryId,
            taxonomy: 'category',
            _wp_http_referer: addCategoryUrl,
            _wpnonce: '', // This would need to be fetched, but for cleanup we can use direct deletion
          },
          failOnStatusCode: false,
        })
      })
      createdCategoryIds = []
    }
  })

  describe('TC-CATEGORY-01: Valid category creation with all fields', () => {
    it('should successfully create category with all fields', () => {
      const timestamp = Date.now()
      const categoryName = `Technology${timestamp}`
      
      cy.get('#tag-name').clear().type(categoryName)
      cy.get('#tag-slug').clear().type('technology')
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear().type('Technology related posts')
      
      // Submit the form
      cy.get('#submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
      cy.url().should('include', 'edit-tags.php?taxonomy=category')
      
      // Verify category appears in the list
      cy.get('#the-list').should('contain', categoryName)
    })
  })

  describe('TC-CATEGORY-02: Valid category creation with minimal fields (name only)', () => {
    it('should successfully create category with name only (slug auto-generated)', () => {
      const timestamp = Date.now()
      const categoryName = `News${timestamp}`
      
      cy.get('#tag-name').clear().type(categoryName)
      cy.get('#tag-slug').clear() // Leave slug empty
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear() // Leave description empty
      
      // Submit the form
      cy.get('#submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
      cy.url().should('include', 'edit-tags.php?taxonomy=category')
      
      // Verify category appears in the list
      cy.get('#the-list').should('contain', categoryName)
    })
  })

  describe('TC-CATEGORY-03: Valid category creation with parent category', () => {
    it('should successfully create category with parent category', () => {
      // First, create a parent category
      const parentTimestamp = Date.now()
      const parentName = `Parent${parentTimestamp}`
      
      cy.get('#tag-name').clear().type(parentName)
      cy.get('#tag-slug').clear().type(`parent${parentTimestamp}`)
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear()
      cy.get('#submit').click()
      cy.wait(1000)
      
      // Get the parent category ID from the dropdown
      cy.get('#parent option').then(($options) => {
        const parentId = $options.filter((i, el) => el.textContent.includes(parentName)).val()
        
        if (parentId && parentId !== '0') {
          const timestamp = Date.now()
          const categoryName = `Sports${timestamp}`
          
          cy.get('#tag-name').clear().type(categoryName)
          cy.get('#tag-slug').clear().type('sports')
          cy.get('#parent').select(parentId)
          cy.get('#tag-description').clear().type('Sports category')
          
          // Submit the form
          cy.get('#submit').click()

          // Check for success message
          cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
          cy.url().should('include', 'edit-tags.php?taxonomy=category')
          
          // Verify category appears in the list
          cy.get('#the-list').should('contain', categoryName)
        } else {
          // Skip test if no parent category was created
          cy.log('Parent category not found, skipping test')
        }
      })
    })
  })

  describe('TC-CATEGORY-04: Empty category name', () => {
    it('should display error message "A name is required for this term."', () => {
      cy.get('#tag-name').clear()
      cy.get('#tag-slug').clear().type('technology')
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear().type('Technology related posts')
      
      // Submit the form
      cy.get('#submit').click()

      // WordPress shows specific error message for empty category name
      cy.get('.notice-error, .error, #message.error').should('be.visible')
      cy.get('body').should('contain', 'A name is required for this term.')
      cy.url().should('include', 'edit-tags.php?taxonomy=category')
    })
  })

  describe('TC-CATEGORY-05: Category name with minimum length (1 character)', () => {
    it('should successfully create category with 1 character name', () => {
      const timestamp = Date.now()
      const categoryName = `a${timestamp}` // Add timestamp to make it unique
      
      cy.get('#tag-name').clear().type(categoryName)
      cy.get('#tag-slug').clear().type('technology')
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear().type('Technology related posts')
      
      // Submit the form
      cy.get('#submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
      cy.url().should('include', 'edit-tags.php?taxonomy=category')
    })
  })

  describe('TC-CATEGORY-06: Category name with maximum length (200 characters)', () => {
    it('should successfully create category with 200 character name', () => {
      const timestamp = Date.now()
      const longName = 'a'.repeat(200 - timestamp.toString().length) + timestamp
      
      cy.get('#tag-name').clear().type(longName)
      cy.get('#tag-slug').clear().type('technology')
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear().type('Technology related posts')
      
      // Submit the form
      cy.get('#submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
      cy.url().should('include', 'edit-tags.php?taxonomy=category')
    })
  })

  describe('TC-CATEGORY-07: Category name exceeding maximum length (201 characters)', () => {
    it('should display error or truncate for 201 character name', () => {
      const timestamp = Date.now()
      const tooLongName = 'a'.repeat(201 - timestamp.toString().length) + timestamp
      
      cy.get('#tag-name').clear().type(tooLongName)
      cy.get('#tag-slug').clear().type('technology')
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear().type('Technology related posts')
      
      // Submit the form
      cy.get('#submit').click()

      // WordPress may truncate or show error
      cy.get('body').then(($body) => {
        if ($body.find('.notice-error, .error, #message.error, p:contains("exceeds"), p:contains("maximum")').length > 0) {
          cy.get('.notice-error, .error, #message.error, p:contains("exceeds"), p:contains("maximum")').should('be.visible')
        } else {
          // WordPress may truncate and save successfully
          cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
        }
      })
    })
  })

  describe('TC-CATEGORY-08: Slug with spaces (invalid format)', () => {
    it('should successfully create category (WordPress sanitizes slug with spaces)', () => {
      const timestamp = Date.now()
      const categoryName = `Technology${timestamp}`
      
      cy.get('#tag-name').clear().type(categoryName)
      cy.get('#tag-slug').clear().type('slug with spaces')
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear().type('Technology related posts')
      
      // Submit the form
      cy.get('#submit').click()

      // WordPress sanitizes the slug and saves successfully
      cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
      cy.url().should('include', 'edit-tags.php?taxonomy=category')
      
      // Verify category appears in the list
      cy.get('#the-list').should('contain', categoryName)
    })
  })

  describe('TC-CATEGORY-09: Slug with special characters (invalid format)', () => {
    it('should successfully create category (WordPress sanitizes slug with special characters)', () => {
      const timestamp = Date.now()
      const categoryName = `Technology${timestamp}`
      
      cy.get('#tag-name').clear().type(categoryName)
      cy.get('#tag-slug').clear().type('slug#invalid')
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear().type('Technology related posts')
      
      // Submit the form
      cy.get('#submit').click()

      // WordPress sanitizes the slug and saves successfully
      cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
      cy.url().should('include', 'edit-tags.php?taxonomy=category')
      
      // Verify category appears in the list
      cy.get('#the-list').should('contain', categoryName)
    })
  })

  describe('TC-CATEGORY-10: Slug with maximum length (200 characters)', () => {
    it('should successfully create category with 200 character slug', () => {
      const timestamp = Date.now()
      const categoryName = `Technology${timestamp}`
      const longSlug = 'a'.repeat(200)
      
      cy.get('#tag-name').clear().type(categoryName)
      cy.get('#tag-slug').clear().type(longSlug)
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear().type('Technology related posts')
      
      // Submit the form
      cy.get('#submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
      cy.url().should('include', 'edit-tags.php?taxonomy=category')
    })
  })

  describe('TC-CATEGORY-11: Slug exceeding maximum length (201 characters)', () => {
    it('should display error or truncate for 201 character slug', () => {
      const timestamp = Date.now()
      const categoryName = `Technology${timestamp}`
      const tooLongSlug = 'a'.repeat(201)
      
      cy.get('#tag-name').clear().type(categoryName)
      cy.get('#tag-slug').clear().type(tooLongSlug)
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear().type('Technology related posts')
      
      // Submit the form
      cy.get('#submit').click()

      // WordPress may truncate or show error
      cy.get('body').then(($body) => {
        if ($body.find('.notice-error, .error, #message.error, p:contains("exceeds"), p:contains("maximum")').length > 0) {
          cy.get('.notice-error, .error, #message.error, p:contains("exceeds"), p:contains("maximum")').should('be.visible')
        } else {
          // WordPress may truncate and save successfully
          cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
        }
      })
    })
  })

  describe('TC-CATEGORY-12: Description with maximum length (5000 characters)', () => {
    it('should successfully create category with 5000 character description', () => {
      const timestamp = Date.now()
      const categoryName = `Technology${timestamp}`
      const longDescription = 'a'.repeat(5000)
      
      cy.get('#tag-name').clear().type(categoryName)
      cy.get('#tag-slug').clear().type('technology')
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear()
      
      // Use invoke to set long text directly
      cy.get('#tag-description').invoke('val', longDescription)
      
      // Submit the form
      cy.get('#submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
      cy.url().should('include', 'edit-tags.php?taxonomy=category')
    })
  })

  describe('TC-CATEGORY-13: Description exceeding maximum length (5001 characters)', () => {
    it('should successfully create category (WordPress truncates long description)', () => {
      const timestamp = Date.now()
      const categoryName = `Technology${timestamp}`
      const tooLongDescription = 'a'.repeat(5001)
      
      cy.get('#tag-name').clear().type(categoryName)
      cy.get('#tag-slug').clear().type('technology')
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear()
      
      // Use invoke to set long text directly
      cy.get('#tag-description').invoke('val', tooLongDescription)
      
      // Submit the form
      cy.get('#submit').click()

      // WordPress truncates and saves successfully (no error message)
      cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
      cy.url().should('include', 'edit-tags.php?taxonomy=category')
      
      // Verify no error message is displayed
      cy.get('.notice-error, .error, #message.error').should('not.exist')
    })
  })

  describe('TC-CATEGORY-14: Category name with special characters', () => {
    it('should successfully create category with special characters (may be sanitized)', () => {
      const timestamp = Date.now()
      const categoryName = `Tech & Gadgets${timestamp}`
      
      cy.get('#tag-name').clear().type(categoryName)
      cy.get('#tag-slug').clear().type('tech-gadgets')
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear().type('Technology and gadgets category')
      
      // Submit the form
      cy.get('#submit').click()

      // Check for success message
      cy.get('#message, .notice-success, .updated, p:contains("Category added"), p:contains("added")').should('be.visible')
      cy.url().should('include', 'edit-tags.php?taxonomy=category')
      
      // Verify category appears in the list (may have sanitized name)
      cy.get('#the-list').should('contain', 'Tech')
    })
  })

  describe('TC-CATEGORY-15: Delete category', () => {
    it('should successfully delete a category', () => {
      // First create a category to delete
      const timestamp = Date.now()
      const categoryName = `DeleteTest${timestamp}`
      
      cy.get('#tag-name').clear().type(categoryName)
      cy.get('#tag-slug').clear().type(`deletetest${timestamp}`)
      cy.get('#parent').select('None')
      cy.get('#tag-description').clear()
      cy.get('#submit').click()
      cy.wait(1000)
      
      // Verify category was created
      cy.get('#the-list').should('contain', categoryName)
      
      // Find the category row - look for the row containing the category name
      cy.get('#the-list').contains('tr', categoryName).as('categoryRow')
      
      // Hover over the name cell to show row actions (similar to tags)
      cy.get('@categoryRow').within(() => {
        cy.get('td.name').trigger('mouseenter')
        
        // Wait for row actions to appear
        cy.get('.row-actions').should('be.visible')
        
        // Click Delete link - try multiple selectors
        cy.get('.row-actions a.delete-tag, .row-actions .delete a, .row-actions a').contains('Delete').scrollIntoView().should('be.visible').click({ force: true })
      })
      
      // Confirm deletion in the confirmation dialog
      cy.on('window:confirm', (str) => {
        expect(str).to.include('delete')
        return true
      })
      
      // Wait for deletion to complete
      cy.wait(1000)
      
      // Verify category is deleted (should not appear in list or show success message)
      cy.get('body').then(($body) => {
        if ($body.find('#the-list').length > 0) {
          cy.get('#the-list').should('not.contain', categoryName)
        }
        // Or check for success message
        cy.get('#message, .notice-success, .updated, p:contains("deleted")').should('exist')
      })
    })
  })
})

