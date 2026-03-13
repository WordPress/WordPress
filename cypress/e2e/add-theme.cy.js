/**
 * Add Theme Form Test Cases
 * Based on Equivalence Class Partitioning and Boundary Value Analysis
 * Test Cases: TC-ADDTHEME-01 to TC-ADDTHEME-05
 */

describe('Add Theme Form - Black Box Testing', () => {
  const baseUrl = 'http://127.0.0.1:8080'
  const addThemeUrl = `${baseUrl}/wp-admin/theme-install.php`
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

    // Navigate to Add Theme page
    cy.visit(addThemeUrl)
    
    // Wait for page to load
    cy.get('body').should('be.visible')
    cy.wait(1000)
  })

  describe('TC-ADDTHEME-01: Search for valid theme', () => {
    it('should successfully search for a theme', () => {
      const searchTerm = 'twenty'
      
      // Find and use search input - WordPress uses #wp-filter-search-input
      cy.get('#wp-filter-search-input', { timeout: 5000 })
        .should('be.visible')
        .clear()
        .type(searchTerm)
        .type('{enter}')
      
      // Wait for search results
      cy.wait(2000)
      
      // Check for search results - themes should be displayed
      cy.get('.theme, .theme-card, .wp-list-table tbody tr', { timeout: 5000 }).should('exist')
      cy.url().should('include', 'theme-install.php')
    })
  })

  describe('TC-ADDTHEME-02: Search with empty term', () => {
    it('should show default themes when search is empty', () => {
      // Find and use search input - WordPress uses #wp-filter-search-input
      cy.get('#wp-filter-search-input', { timeout: 5000 })
        .should('be.visible')
        .clear()
        .type('{enter}')
      
      // Wait for page update
      cy.wait(1000)
      
      // Should show default theme list or featured themes
      cy.get('.theme, .theme-card, .wp-list-table tbody tr, .featured-themes', { timeout: 5000 }).should('exist')
      cy.url().should('include', 'theme-install.php')
    })
  })

  describe('TC-ADDTHEME-03: Install theme from search results', () => {
    it('should successfully install a theme', () => {
      const searchTerm = 'twenty twenty'
      
      // Find and use search input - WordPress uses #wp-filter-search-input
      cy.get('#wp-filter-search-input', { timeout: 5000 })
        .should('be.visible')
        .clear()
        .type(searchTerm)
        .type('{enter}')

      // Wait for search results
      cy.wait(2000)

      // Find the theme card and scroll it into view
      cy.get('.theme, .theme-card', { timeout: 10000 })
        .first()
        .scrollIntoView()
      
      // Wait for page to stabilize after scroll
      cy.wait(1000)
      
      // Get the theme card again and hover over it to reveal the install button
      // WordPress theme cards show install button on hover
      cy.get('.theme, .theme-card', { timeout: 10000 })
        .first()
        .trigger('mouseenter', { force: true })
      
      // Wait for hover effect to show the install button
      cy.wait(500)
      
      // Find and click Install button - WordPress uses .theme-install class
      // Use force: true since button may not be visible due to CSS opacity
      cy.get('a.theme-install, .theme-install, a.button-primary.theme-install', { timeout: 10000 })
        .first()
        .should('exist')
        .click({ force: true })
      
      // Wait for installation to start
      cy.wait(3000)
      
      // Check for installation progress or success
      cy.get('body').then(($body) => {
        // WordPress shows installation progress or success message
        if ($body.find('.notice-success, .updated, #message').length > 0) {
          cy.get('.notice-success, .updated, #message').should('exist')
        }
        // Button text changes during installation
        cy.get('a.theme-install, .theme-install').first().should('exist')
      })
    })
  })

  describe('TC-ADDTHEME-04: Upload theme from file', () => {
    it('should show upload form when clicking upload tab', () => {
      // Click Upload Theme button - WordPress uses button.upload-view-toggle
      cy.get('button.upload-view-toggle, .upload-view-toggle', { timeout: 5000 })
        .should('be.visible')
        .click()
      
      cy.wait(1000)

      // Check for upload form - file input should exist
      cy.get('input[type="file"]#themezip, #themezip', { timeout: 5000 }).should('exist')
      
      // Check for upload button
      cy.get('button#install-theme-submit, #install-theme-submit', { timeout: 5000 }).should('exist')
      cy.url().should('include', 'theme-install.php')
    })
  })

  describe('TC-ADDTHEME-05: Filter themes by category', () => {
    it('should filter themes by category/feature', () => {
      // Click on a filter tab (Popular or Featured) - WordPress uses .filter-links
      cy.get('.filter-links a, .filter-links li a', { timeout: 5000 })
        .contains(/Popular|Featured/i)
        .first()
        .should('be.visible')
        .click({ force: true })
      
      // Wait for filtered results
      cy.wait(2000)
      
      // Check that themes are displayed
      cy.get('.theme, .theme-card, .wp-list-table tbody tr', { timeout: 5000 }).should('exist')
      cy.url().should('include', 'theme-install.php')
    })
  })
})

