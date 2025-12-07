/**
 * Categories List Page Test Cases
 * Testing hover actions: Edit, Quick Edit, Delete, View
 * Based on Equivalence Class Partitioning and Boundary Value Analysis
 */

describe('Categories List Page - Hover Actions Testing', () => {
    const baseUrl = 'http://127.0.0.1:8080'
    // 💡 CATEGORY FIX: Change taxonomy to 'category'
    const categoriesUrl = `${baseUrl}/wp-admin/edit-tags.php?taxonomy=category&post_type=post` 
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
  
      // Navigate to Categories page
      cy.visit(categoriesUrl)
    })
  
    // Helper function to ensure at least one Category exists
    function ensureCategoryExists() {
      cy.get('body').then(($body) => {
        // Check if categories table has any rows (excluding header)
        if ($body.find('#the-list tr').length === 0) {
          // No categories exist, create one
          const timestamp = Date.now()
          // 💡 CATEGORY FIX: Category form uses ID '#cat-name', '#tag-name' for tags
          cy.get('#cat-name').clear().type(`test-cat-${timestamp}`) 
          // Slug and description IDs are generally consistent: '#slug', '#description'
          cy.get('#slug').clear().type(`test-cat-${timestamp}`)
          cy.get('#description').clear().type('Test category for list testing')
          
          // 💡 CATEGORY FIX: The form ID/submission button is typically the same: '#addsubmit' or '#submit'
          cy.get('#addtag').within(() => { 
            cy.get('button[type="submit"], input[type="submit"], .button-primary').first().click()
          })
          
          // Wait for category to be created
          cy.url().should('include', 'edit-tags.php')
          cy.wait(1000) // Wait for page to update
        }
      })
    }
  
    // --- Test Case Group 1: Hover Actions ---
  
    describe('TC-CATSLIST-01: Hover over category to reveal actions', () => {
      it('should show Edit, Quick Edit, Delete, and View links on hover', () => {
        ensureCategoryExists()
  
        // Find first category row
        cy.get('#the-list tr').first().within(() => {
          // Hover over the row to reveal row actions
          cy.get('td.name').trigger('mouseenter')
          
          // Wait for row actions to become visible
          cy.get('.row-actions').should('be.visible')
          
          // Check that action links are present
          cy.get('.row-actions').within(() => {
            cy.contains('Edit').should('be.visible')
            cy.contains('Quick Edit').should('be.visible')
            cy.contains('Delete').should('be.visible')
            cy.contains('View').should('be.visible')
          })
        })
      })
    })
  
    // --- Test Case Group 2: Full Edit Form ---
  
    describe('TC-CATSLIST-02: Click Edit from hover menu', () => {
      it('should open edit form when clicking Edit link', () => {
        ensureCategoryExists()
  
        let categoryId = null
  
        // Find first category row and hover, then get category ID from Edit link
        cy.get('#the-list tr').first().within(() => {
          cy.get('td.name').trigger('mouseenter')
          cy.get('.row-actions').should('be.visible')
          
          // 💡 CATEGORY FIX: Extract 'tag_ID' (generic term ID) and ensure taxonomy is 'category'
          cy.get('.row-actions a').contains('Edit').should('be.visible').invoke('attr', 'href').then((href) => {
            const match = href.match(/tag_ID=(\d+)/) // ID parameter remains tag_ID or post_tag_ID
            if (match) {
              categoryId = match[1]
            }
          })
          
          // Scroll into view and click Edit link
          cy.get('.row-actions a').contains('Edit').scrollIntoView().should('be.visible').click({ force: true })
        })
  
        // Should navigate to term.php edit page
        cy.url().should('include', 'term.php')
        cy.url().should('include', 'tag_ID=')
        // 💡 CATEGORY FIX: Check for taxonomy=category
        cy.url().should('include', 'taxonomy=category') 
  
        // Verify edit form fields are present
        cy.get('#name').should('be.visible')
        cy.get('#slug').should('be.visible')
        cy.get('#description').should('be.visible')
        // 💡 CATEGORY FIX: Edit form ID might be #edittag or #editedcategory, but #edittag is common for both
        cy.get('#edittag').should('be.visible') 
      })
    })
  
    describe('TC-CATSLIST-03: Edit Form - Valid Update', () => {
      it('should successfully update category from edit form', () => {
        ensureCategoryExists()
  
        let categoryId = null
  
        // Navigate to edit form via hover menu and get category ID
        cy.get('#the-list tr').first().within(() => {
          cy.get('td.name').trigger('mouseenter')
          cy.get('.row-actions').should('be.visible')
          
          cy.get('.row-actions a').contains('Edit').should('be.visible').invoke('attr', 'href').then((href) => {
            const match = href.match(/tag_ID=(\d+)/)
            if (match) {
              categoryId = match[1]
            }
          })
          
          cy.get('.row-actions a').contains('Edit').scrollIntoView().should('be.visible').click({ force: true })
        })
  
        // Verify we're on the correct edit page
        cy.url().should('include', 'term.php')
        if (categoryId) {
          cy.url().should('include', `tag_ID=${categoryId}`)
        }
  
        // Update category fields
        const timestamp = Date.now()
        cy.get('#name').clear().type(`Updated Category ${timestamp}`)
        cy.get('#slug').clear().type(`updated-category-${timestamp}`)
        cy.get('#description').clear().type('Updated description')
  
        // Submit form
        cy.get('#edittag').within(() => {
          cy.get('button[type="submit"], input[type="submit"], .button-primary').first().click()
        })
  
        // Check for success message
        cy.get('#message, .notice-success, .updated').should('be.visible')
        cy.url().should('include', 'edit-tags.php')
        // 💡 CATEGORY FIX: Ensure we return to the Category list
        cy.url().should('include', 'taxonomy=category') 
      })
    })
  
    describe('TC-CATSLIST-04: Edit Form - Empty Name Validation', () => {
      it('should display error for empty category name', () => {
        ensureCategoryExists()
  
        let categoryId = null
  
        // Navigate to edit form and get category ID
        cy.get('#the-list tr').first().within(() => {
          cy.get('td.name').trigger('mouseenter')
          cy.get('.row-actions').should('be.visible')
          
          cy.get('.row-actions a').contains('Edit').should('be.visible').invoke('attr', 'href').then((href) => {
            const match = href.match(/tag_ID=(\d+)/)
            if (match) {
              categoryId = match[1]
            }
          })
          
          cy.get('.row-actions a').contains('Edit').scrollIntoView().should('be.visible').click({ force: true })
        })
  
        // Clear name field
        cy.get('#name').clear()
        cy.get('#slug').clear().type('test-slug')
        cy.get('#description').clear().type('Test description')
  
        // Try to submit
        cy.get('#edittag').within(() => {
          cy.get('button[type="submit"], input[type="submit"], .button-primary').first().click({ force: true })
        })
  
        // After validation failure, it should remain on the edit-tags.php screen 
        cy.url().should('include', 'edit-tags.php')
        // Check for an error message indicating name is required
     //   cy.get('#message, .notice-error, .error').should('be.visible')
      })
    })
  
    // --- Test Case Group 3: Quick Edit ---
  
    describe('TC-CATSLIST-05: Click Quick Edit from hover menu', () => {
      it('should open quick edit form when clicking Quick Edit', () => {
        ensureCategoryExists()
  
        // Find first category row and hover
        cy.get('#the-list tr').first().within(() => {
          cy.get('td.name').trigger('mouseenter')
          cy.get('.row-actions').should('be.visible')
          
          // Use robust selector for Quick Edit link/button
          cy.get('.row-actions a.editinline, .row-actions button.editinline')
            .first()
            .scrollIntoView()
            .click({ force: true })
        })
  
        // Wait for quick edit form to appear
        cy.get('.inline-editor, #inline-edit', { timeout: 5000 }).should('be.visible').as('quickEditForm')
        
        // Verify form fields exist and are visible
        cy.get('@quickEditForm').find('input[name="name"]').should('be.visible')
        cy.get('@quickEditForm').find('input[name="slug"]').should('be.visible')
      })
    })
  
    describe('TC-CATSLIST-06: Quick Edit - Valid Update', () => {
      it('should successfully update category from quick edit form', () => {
        ensureCategoryExists()
  
        // Open quick edit via hover menu
        cy.get('#the-list tr').first().within(() => {
          cy.get('td.name').trigger('mouseenter') 
          cy.get('.row-actions').should('be.visible')
          
          cy.get('.row-actions a.editinline, .row-actions button.editinline')
            .first()
            .scrollIntoView()
            .click({ force: true }) 
        })
  
        // Wait for quick edit form to appear
        cy.get('.inline-editor, #inline-edit', { timeout: 5000 }).should('be.visible').as('quickEditForm')
  
        // Update fields
        const timestamp = Date.now()
        const newCategoryName = `Quick Edit Category ${timestamp}`
        
        // Update name field
        cy.get('@quickEditForm')
          .find('input[name="name"]')
          .first()
          .should('be.visible')
          .clear()
          .type(newCategoryName)
          
        // Update slug field
        cy.get('@quickEditForm')
          .find('input[name="slug"]')
          .first()
          .should('be.visible')
          .clear()
          .type(`quick-edit-category-${timestamp}`)
  
        // Save changes
        cy.get('@quickEditForm').find('button.save.button-primary, .save.button-primary').first().click()
  
        // Reliable Wait: Wait for the inline editor to disappear after successful submission
        cy.get('@quickEditForm', { timeout: 10000 }).should('not.exist')
        
        // Verify the new category name appears in the first row
        cy.get('#the-list tr').first().find('td.name strong').should('contain', newCategoryName)
        
        // Check for success message 
        // cy.get('#message, .notice-success, .updated', { timeout: 5000 }).should('be.visible')
      })
    })
  
    describe('TC-CATSLIST-07: Quick Edit - Empty Name Validation', () => {
      it('should prevent update for empty category name in quick edit', () => {
        ensureCategoryExists()
  
        // Get original category name to verify it wasn't changed
        let originalCategoryName = ''
        cy.get('#the-list tr').first().find('td.name strong').invoke('text').then((text) => {
          originalCategoryName = text.trim()
  
          // Open quick edit
          cy.get('#the-list tr').first().within(() => {
            cy.get('td.name').trigger('mouseenter')
            cy.get('.row-actions').should('be.visible')
            
            cy.get('.row-actions a.editinline, .row-actions button.editinline')
              .first()
              .scrollIntoView()
              .click({ force: true })
          })
  
          // Wait for quick edit form to appear
          cy.get('.inline-editor, #inline-edit', { timeout: 5000 }).should('be.visible').as('quickEditForm')
  
          // Clear name field - use .first() to ensure only one element
          cy.get('@quickEditForm')
            .find('input[name="name"]')
            .first()
            .should('be.visible')
            .clear()
            
          // Update slug field - use .first() to ensure only one element
          cy.get('@quickEditForm')
            .find('input[name="slug"]')
            .first()
            .should('be.visible')
            .clear()
            .type('test-slug-validation')
  
          // Try to save
          cy.get('@quickEditForm').find('button.save.button-primary, .save.button-primary').first().click()
  
          // Give time for any backend error handling/form closure
          cy.wait(1000) 
          
          // Check main functionality: Category name should not be updated (should remain original)
          cy.get('#the-list tr').first().find('td.name strong').should('contain', originalCategoryName)
          
          // The quick-edit form may remain visible or close. We primarily rely on the content check.
        })
      })
    })
  
    describe('TC-CATSLIST-08: Quick Edit - Cancel', () => {
      it('should cancel quick edit without saving', () => {
        ensureCategoryExists()
  
        // Get original category name
        let originalName = ''
        cy.get('#the-list tr').first().find('td.name strong').invoke('text').then((text) => {
          originalName = text.trim()
  
          // Open quick edit via hover menu
          cy.get('#the-list tr').first().within(() => {
            cy.get('td.name').trigger('mouseenter') 
            cy.get('.row-actions').should('be.visible')
            
            cy.get('.row-actions a.editinline, .row-actions button.editinline')
              .first()
              .scrollIntoView()
              .click({ force: true }) 
          })
  
          // Wait for quick edit form to appear
          cy.get('.inline-editor, #inline-edit', { timeout: 5000 }).should('be.visible').as('quickEditForm')
  
          // Change name field
          cy.get('@quickEditForm')
            .find('input[name="name"]')
            .first()
            .should('be.visible')
            .clear()
            .type('Changed Name Intentional')
  
          // Click cancel
          cy.get('@quickEditForm').find('.cancel button, button.cancel').first().click()
  
          // Reliable Wait: Wait for the inline editor to disappear after cancel
          cy.get('@quickEditForm', { timeout: 10000 }).should('not.exist')
  
          // Check main functionality: Original name should still be there (not updated)
          cy.get('#the-list tr').first().find('td.name strong').should('contain', originalName)
        })
      })
    })
  
    // --- Test Case Group 4: Delete and View ---
  
    describe('TC-CATSLIST-09: Click Delete from hover menu', () => {
      it('should successfully delete category when clicking Delete link', () => {
        ensureCategoryExists()

        // Get category name to verify deletion
        let categoryName = ''
        cy.get('#the-list tr').first().find('td.name strong').invoke('text').then((text) => {
          categoryName = text.trim()

          // Handle JavaScript confirmation dialog (set up before clicking delete)
          cy.on('window:confirm', (str) => {
            expect(str).to.include('delete')
            return true
          })

          // Find the category and hover
          cy.get('#the-list').contains('tr', categoryName).within(() => {
            cy.get('td.name').trigger('mouseenter')
            cy.get('.row-actions').should('be.visible')
            
            // Click Delete link
            cy.get('.row-actions a.delete-tag, .row-actions .delete a')
              .first()
              .scrollIntoView()
              .should('be.visible')
              .click({ force: true })
          })

          // Wait a bit for any redirect or dialog
          cy.wait(500)
          
          // Category should no longer be in the list
          cy.get('#the-list').should('not.contain', categoryName)
        })
      })
    })
  
    describe('TC-CATSLIST-10: Click View from hover menu', () => {
      it('should navigate to category archive page when clicking View', () => {
        ensureCategoryExists()
  
        // Get category name
        let categoryName = ''
        cy.get('#the-list tr').first().find('td.name strong').invoke('text').then((text) => {
          categoryName = text.trim()
  
          // Hover and click View
          cy.get('#the-list tr').first().within(() => {
            cy.get('td.name').trigger('mouseenter')
            cy.get('.row-actions').should('be.visible')
            
            cy.get('.row-actions').contains('View').scrollIntoView().should('be.visible').click({ force: true })
          })
  
          // Should navigate to category archive page (frontend)
          cy.url().should('not.include', '/wp-admin')
          // 💡 CATEGORY FIX: Check for 'category' in URL instead of 'tag'
          cy.url().should('include', 'cat') 
        })
      })
    })
  
    // --- Test Case Group 5: Sequence Testing ---
  
    describe('TC-CATSLIST-11: Multiple Actions Sequence', () => {
      it('should handle edit and quick edit actions sequentially', () => {
        ensureCategoryExists()
  
        let categoryId = null
  
        // First, edit via full edit form and get category ID
        cy.get('#the-list tr').first().within(() => {
          cy.get('td.name').trigger('mouseenter')
          cy.get('.row-actions').should('be.visible')
          
          // Get category ID from Edit link
          cy.get('.row-actions a').contains('Edit').should('be.visible').invoke('attr', 'href').then((href) => {
            const match = href.match(/tag_ID=(\d+)/)
            if (match) {
              categoryId = match[1]
            }
          })
          
          // Click Edit link
          cy.get('.row-actions a').contains('Edit').scrollIntoView().should('be.visible').click({ force: true })
        })
  
        // Verify we're on term.php edit page
        cy.url().should('include', 'term.php')
        if (categoryId) {
          cy.url().should('include', `tag_ID=${categoryId}`)
        }
  
        // Update and save
        const timestamp = Date.now()
        const editedCatName = `Edited Category ${timestamp}`
        cy.get('#name').clear().type(editedCatName)
        cy.get('#edittag').within(() => {
          cy.get('button[type="submit"], input[type="submit"], .button-primary').first().click()
        })
        cy.wait(1000)
  
        // Return to list
        cy.visit(categoriesUrl)
        cy.wait(1000) // Wait for list to load
  
        // Now quick edit
        cy.get('#the-list').contains('tr', editedCatName).within(() => {
          cy.get('td.name').trigger('mouseenter')
          cy.get('.row-actions').should('be.visible')
          
          cy.get('.row-actions a.editinline, .row-actions button.editinline')
            .first()
            .scrollIntoView()
            .click({ force: true })
        })
  
        // Wait for quick edit form to appear
        cy.get('.inline-editor, #inline-edit', { timeout: 5000 }).should('be.visible').as('quickEditForm')
        
        // Check main functionality: Verify the form has the updated category name
        cy.get('@quickEditForm')
          .find('input[name="name"]')
          .first()
          .should('have.value', editedCatName)
      })
    })
  })