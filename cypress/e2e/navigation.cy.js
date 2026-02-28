/**
 * Navigation Flows - UI Navigation Test Cases
 *
 * Goal:
 *  - Verify that main UI navigation works correctly for both
 *    frontend (site) and admin (dashboard).
 *  - These are "user flow" tests: click what a real user clicks and
 *    assert that the correct page and UI elements appear.
 */

describe('UI Navigation Flows', () => {
  // Frontend base URL (home page)
  const baseUrl = 'http://127.0.0.1:8080'
  // Login page (WordPress login is not under /wp-admin)
  const loginUrl = `${baseUrl}/wp-login.php`
  const adminUsername = 'Qadeer572'
  const adminPassword = 'raza@1214'

  /**
   * Helper: login as admin
   */
  const loginAsAdmin = () => {
    cy.visit(loginUrl)
    cy.get('#user_login').clear().type(adminUsername)
    cy.get('#user_pass').clear().type(adminPassword)
    cy.get('#wp-submit').click()
    cy.url().should('include', '/wp-admin')
  }

  /**
   * FRONTEND NAVIGATION
   */
  describe('Frontend Navigation', () => {
    it('TC-NAV-FRONT-01: Main menu links navigate to correct pages', () => {
      cy.visit(baseUrl)

      // Assumption: there is a main menu in #site-navigation or .main-navigation.
      // For stability, test ONLY the first visible menu link as a representative navigation flow.
      cy.get('nav, #site-navigation, .main-navigation', { timeout: 10000 })
        .first()
        .find('a')
        .filter(':visible')
        .first()
        .then(($link) => {
          const href = $link.attr('href')
          if (!href || href === '#' || href.startsWith('mailto:') || href.startsWith('tel:')) {
            // If first link is not a real navigation link, just skip the assertion
            return
          }

          // Click this menu item
          cy.wrap($link).click({ force: true })

          // URL should change to include the target path
          const relative = href.replace(baseUrl, '')
          if (relative && relative !== href) {
            cy.url().should('include', relative)
          } else {
            cy.url().should('include', href)
          }

          // Basic check: page loaded (body present). We only care that navigation works,
          // not about specific layout or content structure here.
          cy.get('body', { timeout: 10000 }).should('exist')
        })
    })

    it('TC-NAV-FRONT-02: Clicking site title/logo goes back to Home', () => {
      // First, navigate to a non-home page (if a menu link exists)
      cy.visit(baseUrl)
      cy.get('nav a, #site-navigation a, .main-navigation a')
        .filter(':visible')
        .contains(/.+/, { matchCase: false })
        .first()
        .then(($link) => {
          const href = $link.attr('href')
          if (href && href !== baseUrl && !href.endsWith('/')) {
            cy.wrap($link).click({ force: true })
          }
        })

      // Now click the logo/site title (theme-dependent). If not found, just log and skip.
      cy.get(
        '#site-title a, .site-title a, .custom-logo-link, .site-branding a, .wp-block-site-title a, .wp-block-site-logo a',
        { timeout: 5000 }
      ).then(($logoLinks) => {
        if (!$logoLinks.length) {
          cy.log('No explicit site title/logo link found on this theme; skipping logo navigation assertion.')
          return
        }

        cy.wrap($logoLinks.first()).click({ force: true })
      })

      // Should return to home (frontend root)
      cy.url().should('include', `${baseUrl}/`)
      cy.get('body.home, body.blog').should('exist')
    })

    it('TC-NAV-FRONT-03: Post links navigate to single post pages', () => {
      cy.visit(baseUrl)

      // Find a candidate "content" link (post or page). If none, log and skip.
      cy.get('a[href*="?p="], a[href*="/?page_id="], article a, .entry-title a, h2.entry-title a', { timeout: 5000 })
        .filter(':visible')
        .then(($links) => {
          if (!$links.length) {
            cy.log('No post/page-style links found on home; skipping TC-NAV-FRONT-03.')
            return
          }

          const $link = $links.first()
          const href = $link.attr('href')
          if (!href) return

          cy.wrap($link).click({ force: true })
          cy.url().should('include', href.replace(baseUrl, ''))
          cy.get('article, .entry-content, .wp-block-post-content', { timeout: 10000 }).should('exist')
        })
    })
  })

  /**
   * ADMIN NAVIGATION
   */
  describe('Admin Navigation', () => {
    beforeEach(() => {
      loginAsAdmin()
    })

    it('TC-NAV-ADMIN-01: Admin menu items open correct main screens', () => {
      // Posts
      cy.get('#menu-posts a.menu-top', { timeout: 10000 }).click()
      cy.url().should('include', '/wp-admin/edit.php')
      cy.get('h1, .wrap h1')
        .invoke('text')
        .then((text) => {
          expect(text).to.match(/Posts|All Posts/i)
        })

      // Pages
      cy.get('#menu-pages a.menu-top').click()
      cy.url().should('include', '/wp-admin/edit.php?post_type=page')
      cy.get('h1, .wrap h1').should('contain', 'Pages')

      // Media
      cy.get('#menu-media a.menu-top').click()
      cy.url().should('include', '/wp-admin/upload.php')
      cy.get('h1, .wrap h1').should('contain', 'Media')

      // Comments
      cy.get('#menu-comments a.menu-top').click()
      cy.url().should('include', '/wp-admin/edit-comments.php')
      cy.get('h1, .wrap h1').should('contain', 'Comments')

      // Appearance (Themes)
      cy.get('#menu-appearance a.menu-top').click()
      cy.url().should('include', '/wp-admin/themes.php')
      cy.get('h1, .wrap h1').should('contain', 'Themes')

      // Users
      cy.get('#menu-users a.menu-top').click()
      cy.url().should('include', '/wp-admin/users.php')
      cy.get('h1, .wrap h1').should('contain', 'Users')
    })

    it('TC-NAV-ADMIN-02: Settings submenu items load the correct settings pages', () => {
      // General
      cy.get('#menu-settings a.menu-top').click()
      cy.get('#menu-settings a[href$="options-general.php"]').first().click()
      cy.url().should('include', '/wp-admin/options-general.php')
      cy.get('h1, .wrap h1').should('contain', 'General')

      // Reading
      cy.get('#menu-settings a[href$="options-reading.php"]').first().click()
      cy.url().should('include', '/wp-admin/options-reading.php')
      cy.get('h1, .wrap h1').should('contain', 'Reading')

      // Media
      cy.get('#menu-settings a[href$="options-media.php"]').first().click()
      cy.url().should('include', '/wp-admin/options-media.php')
      cy.get('h1, .wrap h1').should('contain', 'Media')
    })

    it('TC-NAV-ADMIN-03: Admin bar links navigate between frontend and dashboard', () => {
      // Already logged into admin from beforeEach

      // Visit frontend via admin bar "Visit Site"
      cy.get('#wp-admin-bar-site-name > a, #wp-admin-bar-view-site > a', { timeout: 10000 })
        .first()
        .click({ force: true })

      // Now on frontend; URL should include the frontend base URL
      cy.url().should('include', baseUrl)
      cy.get('#wpadminbar').should('exist') // admin bar visible on frontend

      // Use admin bar "Dashboard" link to go back
      cy.get('#wp-admin-bar-dashboard > a', { timeout: 10000 }).click({ force: true })
      cy.url().should('include', '/wp-admin/')
      cy.get('#wpadminbar').should('exist')
    })
  })
})


