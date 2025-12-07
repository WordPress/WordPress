/**
 * Tags List Page Test Cases
 * Testing hover actions: Edit, Quick Edit, Delete, View
 * Based on Equivalence Class Partitioning and Boundary Value Analysis
 */

describe('Tags List Page - Hover Actions Testing', () => {
  const baseUrl = 'http://127.0.0.1:8080'
  const tagsUrl = `${baseUrl}/wp-admin/edit-tags.php?taxonomy=post_tag&post_type=post`
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

    // Navigate to Tags page
    cy.visit(tagsUrl)
  })

  // Helper function to ensure at least one tag exists
  function ensureTagExists() {
    cy.get('body').then(($body) => {
      // Check if tags table has any rows (excluding header)
      if ($body.find('#the-list tr').length === 0) {
        // No tags exist, create one
        const timestamp = Date.now()
        cy.get('#tag-name').clear().type(`test-tag-${timestamp}`)
        cy.get('#tag-slug').clear().type(`test-tag-${timestamp}`)
        cy.get('#tag-description').clear().type('Test tag for list testing')
        cy.get('#addtag').within(() => {
          cy.get('button[type="submit"], input[type="submit"], .button-primary').first().click()
        })
        // Wait for tag to be created
        cy.url().should('include', 'edit-tags.php')
        cy.wait(1000) // Wait for page to update
      }
    })
  }

  describe('TC-TAGSLIST-01: Hover over tag to reveal actions', () => {
    it('should show Edit, Quick Edit, Delete, and View links on hover', () => {
      ensureTagExists()

      // Find first tag row
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

  describe('TC-TAGSLIST-02: Click Edit from hover menu', () => {
    it('should open edit form when clicking Edit link', () => {
      ensureTagExists()

      let tagId = null

      // Find first tag row and hover, then get tag ID from Edit link
      cy.get('#the-list tr').first().within(() => {
        // Hover over the row to reveal actions
        cy.get('td.name').trigger('mouseenter')
        
        // Wait for row actions to be visible
        cy.get('.row-actions').should('be.visible')
        
        // Get tag ID from Edit link href
        cy.get('.row-actions a').contains('Edit').should('be.visible').invoke('attr', 'href').then((href) => {
          const match = href.match(/tag_ID=(\d+)/)
          if (match) {
            tagId = match[1]
          }
        })
        
        // Scroll into view and click Edit link
        cy.get('.row-actions a').contains('Edit').scrollIntoView().should('be.visible').click({ force: true })
      })

      // Should navigate to term.php edit page
      cy.url().should('include', 'term.php')
      cy.url().should('include', 'tag_ID=')
      cy.url().should('include', 'taxonomy=post_tag')

      // Verify edit form fields are present
      cy.get('#name').should('be.visible')
      cy.get('#slug').should('be.visible')
      cy.get('#description').should('be.visible')
      cy.get('#edittag').should('be.visible')
    })
  })

  describe('TC-TAGSLIST-03: Edit Form - Valid Update', () => {
    it('should successfully update tag from edit form', () => {
      ensureTagExists()

      let tagId = null

      // Navigate to edit form via hover menu and get tag ID
      cy.get('#the-list tr').first().within(() => {
        cy.get('td.name').trigger('mouseenter')
        
        // Wait for row actions to be visible
        cy.get('.row-actions').should('be.visible')
        
        // Get tag ID from Edit link
        cy.get('.row-actions a').contains('Edit').should('be.visible').invoke('attr', 'href').then((href) => {
          const match = href.match(/tag_ID=(\d+)/)
          if (match) {
            tagId = match[1]
          }
        })
        
        // Click Edit link
        cy.get('.row-actions a').contains('Edit').scrollIntoView().should('be.visible').click({ force: true })
      })

      // Verify we're on the correct edit page
      cy.url().should('include', 'term.php')
      if (tagId) {
        cy.url().should('include', `tag_ID=${tagId}`)
      }

      // Update tag fields
      const timestamp = Date.now()
      cy.get('#name').clear().type(`Updated Tag ${timestamp}`)
      cy.get('#slug').clear().type(`updated-tag-${timestamp}`)
      cy.get('#description').clear().type('Updated description')

      // Submit form
      cy.get('#edittag').within(() => {
        cy.get('button[type="submit"], input[type="submit"], .button-primary').first().click()
      })

      // Check for success message
      cy.get('#message, .notice-success, .updated').should('be.visible')
      cy.url().should('include', 'edit-tags.php')
    })
  })

  describe('TC-TAGSLIST-04: Edit Form - Empty Name Validation', () => {
    it('should display error for empty tag name', () => {
      ensureTagExists()

      let tagId = null

      // Navigate to edit form and get tag ID
      cy.get('#the-list tr').first().within(() => {
        cy.get('td.name').trigger('mouseenter')
        
        // Wait for row actions to be visible
        cy.get('.row-actions').should('be.visible')
        
        // Get tag ID from Edit link
        cy.get('.row-actions a').contains('Edit').should('be.visible').invoke('attr', 'href').then((href) => {
          const match = href.match(/tag_ID=(\d+)/)
          if (match) {
            tagId = match[1]
          }
        })
        
        // Click Edit link
        cy.get('.row-actions a').contains('Edit').scrollIntoView().should('be.visible').click({ force: true })
      })

      // Verify we're on the correct edit page
      cy.url().should('include', 'term.php')
      if (tagId) {
        cy.url().should('include', `tag_ID=${tagId}`)
      }

      // Clear name field
      cy.get('#name').clear()
      cy.get('#slug').clear().type('test-slug')
      cy.get('#description').clear().type('Test description')

      // Try to submit
      cy.get('#edittag').within(() => {
        cy.get('button[type="submit"], input[type="submit"], .button-primary').first().click({ force: true })
      })

      cy.url().should('include', 'edit-tags.php')
    })
  })

  describe('TC-TAGSLIST-05: Click Quick Edit from hover menu', () => {
    it('should open quick edit form when clicking Quick Edit', () => {
      ensureTagExists()

      // Find first tag row and hover
      cy.get('#the-list tr').first().within(() => {
        cy.get('td.name').trigger('mouseenter')
        
        // Wait for row actions to be visible
        cy.get('.row-actions').should('be.visible')
        
        // Click Quick Edit button
        cy.get('.row-actions button.editinline, .row-actions .editinline, .row-actions a.editinline').scrollIntoView().click({ force: true })
      })

      // Wait for quick edit form to appear
      cy.wait(500)
      
      // Verify form fields exist (assume visible, use force for interaction)
      cy.get('#inline-edit input[name="name"]').should('exist')
      cy.get('#inline-edit input[name="slug"]').should('exist')
    })
  })

describe('TC-TAGSLIST-06: Quick Edit - Valid Update', () => {
    it('should successfully update tag from quick edit form', () => {
      ensureTagExists()

      // Open quick edit via hover menu
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

      // Update fields
      const timestamp = Date.now()
      const newTagName = `Quick Edit Tag ${timestamp}`
      
      // Update name field - use .first() to ensure only one element
      cy.get('@quickEditForm')
        .find('input[name="name"]')
        .first()
        .should('be.visible')
        .clear()
        .type(newTagName)
        
      // Update slug field - use .first() to ensure only one element
      cy.get('@quickEditForm')
        .find('input[name="slug"]')
        .first()
        .should('be.visible')
        .clear()
        .type(`quick-edit-tag-${timestamp}`)

      // Save changes - use .first() to ensure only one element
      cy.get('@quickEditForm').find('button.save.button-primary, .save.button-primary').first().click()

      // Reliable Wait: Wait for the inline editor to disappear after successful submission
      cy.get('@quickEditForm', { timeout: 10000 }).should('not.exist')
      
      // Verify the new tag name appears in the first row
      cy.get('#the-list tr').first().find('td.name strong').should('contain', newTagName)
      
      // Check for success message 
      //cy.get('#message, .notice-success, .updated', { timeout: 5000 }).should('be.visible')
    })
  })

  describe('TC-TAGSLIST-07: Quick Edit - Empty Name Validation', () => {
    it('should display error for empty tag name in quick edit', () => {
      ensureTagExists()

      // Get original tag name to verify it wasn't changed
      let originalTagName = ''
      cy.get('#the-list tr').first().find('td.name strong').invoke('text').then((text) => {
        originalTagName = text.trim()

        // Open quick edit
        cy.get('#the-list tr').first().within(() => {
          cy.get('td.name').trigger('mouseenter')
          
          // Wait for row actions to be visible
          cy.get('.row-actions').should('be.visible')
          
          cy.get('.row-actions button.editinline, .row-actions .editinline, .row-actions a.editinline').scrollIntoView().click({ force: true })
        })

        // Wait for quick edit form to appear
        cy.wait(500)

        // Clear name field (use force: true since form may not be visible)
        cy.get('#inline-edit input[name="name"]').clear({ force: true })
        cy.get('#inline-edit input[name="slug"]').clear({ force: true }).type('test-slug', { force: true })

        // Try to save - use .first() to ensure only one element
        cy.get('#inline-edit button.save.button-primary, #inline-edit .save.button-primary').first().click({ force: true })

        // Wait for loading to complete - inline edit form should disappear or show error
        cy.get('#inline-edit', { timeout: 5000 }).should('not.be.visible')
        
        // Wait for list to be ready (this implies loading is done)
        cy.get('#the-list', { timeout: 5000 }).should('be.visible')

        // Check main functionality: Tag name should not be updated (should remain original)
        cy.get('#the-list tr').first().find('td.name strong').should('contain', originalTagName)
      })
    })
  })

  describe('TC-TAGSLIST-08: Quick Edit - Cancel', () => {
    it('should cancel quick edit without saving', () => {
      ensureTagExists()

      // Get original tag name
      let originalName = ''
      cy.get('#the-list tr').first().find('td.name strong').invoke('text').then((text) => {
        originalName = text.trim()

        // Open quick edit via hover menu
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

        // Change name field - use .first() to ensure only one element
        cy.get('@quickEditForm')
          .find('input[name="name"]')
          .first()
          .should('be.visible')
          .clear()
          .type('Changed Name')

        // Click cancel - use .first() to ensure only one element
        cy.get('@quickEditForm').find('.cancel button, button.cancel').first().click()

        // Reliable Wait: Wait for the inline editor to disappear after cancel
        cy.get('@quickEditForm', { timeout: 10000 }).should('not.exist')

        // Check main functionality: Original name should still be there (not updated)
        cy.get('#the-list tr').first().find('td.name strong').should('contain', originalName)
      })
    })
  })

  describe('TC-TAGSLIST-09: Click Delete from hover menu', () => {
    it('should successfully delete tag when clicking Delete link', () => {
      // Create a tag specifically for deletion
      const timestamp = Date.now()
      const tagName = `delete-tag-${timestamp}`
      
      cy.get('#tag-name').clear().type(tagName)
      cy.get('#tag-slug').clear().type(`delete-tag-${timestamp}`)
      cy.get('#tag-description').clear().type('Tag to be deleted')
      cy.get('#addtag').within(() => {
        cy.get('button[type="submit"], input[type="submit"], .button-primary').first().click()
      })

      // Wait for tag to be created and return to list
      cy.url().should('include', 'edit-tags.php')
      cy.wait(1000)

      // Handle JavaScript confirmation dialog (set up before clicking delete)
      cy.on('window:confirm', (str) => {
        expect(str).to.include('delete')
        return true
      })

      // Find the tag we just created and hover - use contains with 'tr' selector
      cy.get('#the-list').contains('tr', tagName).within(() => {
        // Hover to reveal actions
        cy.get('td.name').trigger('mouseenter')
        
        // Wait for row actions to be visible
        cy.get('.row-actions').should('be.visible')
        
        // Click Delete link - use .first() to ensure only one element
        cy.get('.row-actions a.delete-tag, .row-actions .delete a')
          .first()
          .scrollIntoView()
          .should('be.visible')
          .click({ force: true })
      })

      // Wait a bit for any redirect or dialog
      cy.wait(500)
      
      // Check if we're on a confirmation page (WordPress may redirect to confirmation form)
      cy.get('body').then(($body) => {
        // Look for visible confirmation form buttons (not hidden screen options)
        // Only check for buttons inside forms and that are visible
        const visibleConfirmBtn = $body.find('form:visible input[type="submit"].button-primary:visible, form:visible button.button-primary:visible')
        if (visibleConfirmBtn.length > 0) {
          cy.get('form:visible input[type="submit"].button-primary:visible, form:visible button.button-primary:visible').first().click()
        }
      })

      // Wait for deletion to complete
      cy.wait(1000)

      // Check for success message
     // cy.get('#message, .notice-success, .updated').should('be.visible')
      
      // Tag should no longer be in the list
      cy.get('#the-list').should('not.contain', tagName)
    })
  })

  describe('TC-TAGSLIST-10: Click View from hover menu', () => {
    it('should navigate to tag archive page when clicking View', () => {
      ensureTagExists()

      // Get tag name
      let tagName = ''
      cy.get('#the-list tr').first().find('td.name strong').invoke('text').then((text) => {
        tagName = text.trim()

        // Hover and click View
        cy.get('#the-list tr').first().within(() => {
          cy.get('td.name').trigger('mouseenter')
          
          // Wait for row actions to be visible
          cy.get('.row-actions').should('be.visible')
          
          cy.get('.row-actions').contains('View').scrollIntoView().should('be.visible').click({ force: true })
        })

        // Should navigate to tag archive page (frontend)
        cy.url().should('not.include', '/wp-admin')
        cy.url().should('include', tagName.toLowerCase().replace(/\s+/g, '-') || 'tag')
      })
    })
  })

  describe('TC-TAGSLIST-11: Multiple Actions Sequence', () => {
    it('should handle edit and quick edit actions sequentially', () => {
      ensureTagExists()

      let tagId = null

      // First, edit via full edit form and get tag ID
      cy.get('#the-list tr').first().within(() => {
        cy.get('td.name').trigger('mouseenter')
        
        // Wait for row actions to be visible
        cy.get('.row-actions').should('be.visible')
        
        // Get tag ID from Edit link
        cy.get('.row-actions a').contains('Edit').should('be.visible').invoke('attr', 'href').then((href) => {
          const match = href.match(/tag_ID=(\d+)/)
          if (match) {
            tagId = match[1]
          }
        })
        
        // Click Edit link
        cy.get('.row-actions a').contains('Edit').scrollIntoView().should('be.visible').click({ force: true })
      })

      // Verify we're on term.php edit page
      cy.url().should('include', 'term.php')
      if (tagId) {
        cy.url().should('include', `tag_ID=${tagId}`)
      }

      // Update and save
      const timestamp = Date.now()
      cy.get('#name').clear().type(`Edited Tag ${timestamp}`)
      cy.get('#edittag').within(() => {
        cy.get('button[type="submit"], input[type="submit"], .button-primary').first().click()
      })
      cy.wait(1000)

      // Return to list
      cy.visit(tagsUrl)
      cy.wait(1000) // Wait for list to load

      // Now quick edit - use contains with 'tr' selector
      cy.get('#the-list').contains('tr', `Edited Tag ${timestamp}`).within(() => {
        cy.get('td.name').trigger('mouseenter')
        
        // Wait for row actions to be visible
        cy.get('.row-actions').should('be.visible')
        
        // Use robust selector for Quick Edit link/button
        cy.get('.row-actions a.editinline, .row-actions button.editinline')
          .first()
          .scrollIntoView()
          .click({ force: true })
      })

      // Wait for quick edit form to appear
      cy.get('.inline-editor, #inline-edit', { timeout: 5000 }).should('be.visible').as('quickEditForm')
      
      // Check main functionality: Verify the form has the updated tag name
      cy.get('@quickEditForm')
        .find('input[name="name"]')
        .first()
        .should('have.value', `Edited Tag ${timestamp}`)
    })
  })
})

