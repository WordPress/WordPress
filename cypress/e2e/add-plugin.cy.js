/**
 * Plugin Management - Add / Install / Activate / Deactivate / Upload
 *
 * Covers main user flows your instructor expects for "plugin" testing:
 *  - Search for a plugin
 *  - Install a plugin from search results
 *  - Activate and deactivate a plugin from Installed Plugins
 *  - Open the Upload Plugin form
 *  - (Optional) Navigate to a plugin's Settings page if available
 */

describe('Plugin Management Flows', () => {
  const baseUrl = 'http://127.0.0.1:8080'
  const loginUrl = `${baseUrl}/wp-login.php`
  const adminUsername = 'Qadeer572'
  const adminPassword = 'raza@1214'

  const loginAsAdmin = () => {
    cy.visit(loginUrl)
    cy.get('#user_login').clear().type(adminUsername)
    cy.get('#user_pass').clear().type(adminPassword)
    cy.get('#wp-submit').click()
    cy.url().should('include', '/wp-admin')
  }

  const goToAddNewPlugins = () => {
    // Go directly to Add Plugins screen (more stable than clicking "Add New" button)
    cy.visit(`${baseUrl}/wp-admin/plugin-install.php`)
    cy.url().should('include', '/wp-admin/plugin-install.php')
  }

  beforeEach(() => {
    loginAsAdmin()
  })

  describe('TC-PLUGIN-01: Search for a plugin', () => {
    it('should find plugins when searching with a valid term', () => {
      goToAddNewPlugins()

      const searchTerm = 'seo'

      // Search input on Add Plugins screen
      cy.get('#search-plugins, input[name="s"]', { timeout: 10000 })
        .should('be.visible')
        .clear()
        .type(searchTerm)
        .type('{enter}')

      // Wait for results and assert plugin cards/list show up
      cy.get('.plugin-card, .wp-list-table.plugins tbody tr, .plugin-card-top', {
        timeout: 20000,
      }).should('exist')

      cy.url().should('include', 'plugin-install.php')
    })
  })

  describe('TC-PLUGIN-02: Install a plugin from search results', () => {
    it('should install a plugin from the Add Plugins screen', () => {
      goToAddNewPlugins()

      const searchTerm = 'contact'

      cy.get('#search-plugins, input[name="s"]', { timeout: 10000 })
        .should('be.visible')
        .clear()
        .type(searchTerm)
        .type('{enter}')

      // Wait for search results
      cy.get('.plugin-card, .wp-list-table.plugins tbody tr, .plugin-card-top', {
        timeout: 20000,
      }).should('exist')

      // Click "Install Now" on the first plugin card/button
      cy.get('.plugin-card .install-now, .plugin-card a.button.install-now, .install-now', {
        timeout: 15000,
      })
        .filter(':visible')
        .first()
        .click({ force: true })

      // After clicking, button text should eventually change to "Activate" or "Installed"
      cy.get('.plugin-card .activate-now, .plugin-card .button[disabled], .plugin-card .installed', {
        timeout: 30000,
      }).should('exist')
    })
  })

  describe('TC-PLUGIN-03: Activate and deactivate a plugin', () => {
    it('should activate and then deactivate a plugin from Installed Plugins list', () => {
      // Go to Installed Plugins screen
      cy.get('#menu-plugins a.menu-top', { timeout: 10000 }).click()
      cy.url().should('include', '/wp-admin/plugins.php')

      // Try to find a plugin that has an "Activate" link first
      cy.get('table.plugins tbody tr', { timeout: 10000 })
        .filter(':visible')
        .then(($rows) => {
          let rowWithActivate = null
          let pluginName = ''
          $rows.each((_, row) => {
            const $row = Cypress.$(row)
            if ($row.find('a.activate, .row-actions .activate a').length > 0) {
              rowWithActivate = $row
              // Try to capture plugin name from the row
              const nameFromTitle = $row.find('.plugin-title strong, .row-title').first().text()
              pluginName = nameFromTitle || $row.text().trim()
              return false
            }
          })

          if (!rowWithActivate) {
            cy.log('No plugin with an Activate link found; skipping activate/deactivate flow.')
            return
          }

          // Activate using the stored row reference
          cy.wrap(rowWithActivate).find('a.activate, .row-actions .activate a').first().click({ force: true })

          // Check for activation notice
          cy.get('.notice-success, .updated, #message', { timeout: 10000 })
            .should('exist')
            .and('contain.text', 'activated')

          // After activation, the page reloads, so we must re-find the plugin row
          if (pluginName) {
            cy.contains('table.plugins tbody tr', pluginName, { timeout: 10000 })
              .as('activatedRow')
          } else {
            // Fallback: just take the first active row
            cy.get('table.plugins tbody tr.active', { timeout: 10000 })
              .first()
              .as('activatedRow')
          }

          // Now Deactivate the same plugin
          cy.get('@activatedRow')
            .find('a.deactivate, .row-actions .deactivate a')
            .first()
            .click({ force: true })

          cy.get('.notice-success, .updated, #message', { timeout: 10000 })
            .should('exist')
            .and('contain.text', 'deactivated')
        })
    })
  })

  describe('TC-PLUGIN-04: Open Upload Plugin form', () => {
    it('should show the upload plugin form on Add Plugins screen', () => {
      goToAddNewPlugins()

      // Click "Upload Plugin" button/tab
      cy.get('a.upload-view-toggle, a[href*="upload-plugin"], .upload', { timeout: 10000 })
        .filter(':visible')
        .first()
        .click({ force: true })

      // Upload form elements should be visible
      cy.get('form#upload-plugin, form[enctype*="multipart/form-data"]', { timeout: 10000 }).should('exist')
      cy.get('input[type="file"][name="pluginzip"], #pluginzip', { timeout: 10000 }).should('exist')
      cy.get('input[type="submit"], button[type="submit"]', { timeout: 10000 }).should('exist')
    })
  })

  describe('TC-PLUGIN-05: Navigate to a plugin Settings page (if available)', () => {
    it('should open the settings page for a plugin that provides a Settings link', () => {
      // Go to Installed Plugins
      cy.get('#menu-plugins a.menu-top', { timeout: 10000 }).click()
      cy.url().should('include', '/wp-admin/plugins.php')

      // Find first plugin row that has a "Settings" link
      cy.get('table.plugins tbody tr', { timeout: 10000 })
        .filter(':visible')
        .then(($rows) => {
          let rowWithSettings = null
          $rows.each((_, row) => {
            const $row = Cypress.$(row)
            if ($row.find('.row-actions .settings a, a[aria-label*="Settings"]').length > 0) {
              rowWithSettings = $row
              return false
            }
          })

          if (!rowWithSettings) {
            cy.log('No plugin Settings link found; skipping TC-PLUGIN-05.')
            return
          }

          const $row = rowWithSettings

          cy.wrap($row)
            .find('.row-actions .settings a, a[aria-label*="Settings"]')
            .first()
            .click({ force: true })

          // Assert we navigated away from plugins.php and onto some settings page
          cy.url().should('not.include', '/wp-admin/plugins.php')
          cy.get('h1, .wrap h1, .wrap h2', { timeout: 10000 }).should('exist')
        })
    })
  })
})


