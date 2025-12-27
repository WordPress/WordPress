describe('WordPress Post Editor Functionality Tests', () => {
  const postUrl = 'http://127.0.0.1:8080/wp-admin/post-new.php';
  const loginUrl = 'http://127.0.0.1:8080/wp-login.php';
  const username = 'Qadeer572';
  const password = 'raza@1214';
  
  // 💡 FINAL FIX 1: Set a very high global timeout to give all commands a chance
  Cypress.config('defaultCommandTimeout', 30000); 

  // Handle uncaught exceptions from WordPress editor
  Cypress.on('uncaught:exception', (err, runnable) => {
    // Suppress common Gutenberg initialization errors
    if (err.message && (
        err.message.includes('documentElement') || 
        err.message.includes('ResizeObserver') || 
        err.message.includes('Cannot read property') ||
        err.message.includes('Cannot destructure property')
    )) {
      return false;
    }
    return true;
  });

// -------------------------------------------------------------------
// Helper: Get Title Input
// Use the core Gutenberg class for the post title <h1>. This is much
// simpler and more robust than matching every attribute and class.
// -------------------------------------------------------------------
  const getTitleInput = () =>
    cy.get('h1.editor-post-title__input', { timeout: 30000 }).first();

// -------------------------------------------------------------------
// Helper: Get Content Area
// -------------------------------------------------------------------
  const getEditorArea = () =>
    // Targets the rich text editable area inside the default paragraph block
    cy.get('.block-editor-default-block-appender .block-editor-rich-text__editable, .block-editor-block-list__block[data-type="core/paragraph"] .block-editor-rich-text__editable').first().should('exist');

  const getSaveDraftButton = () =>
    // Simplifies the save button selector to the standard class
    cy.get('button.editor-post-save-draft', { timeout: 10000 }).first();

// -------------------------------------------------------------------
// Helper: visit the editor page as an admin user
// -------------------------------------------------------------------
  const visitPostEditor = () => {
    cy.visit(loginUrl);
    cy.get('#user_login').clear().type(username);
    cy.get('#user_pass').clear().type(password);
    cy.get('#wp-submit').click();

    cy.url().should('include', '/wp-admin');

    cy.visit(postUrl);

    // Wait for the main editor wrapper to load
    cy.get(".edit-post-layout", { timeout: 30000 }).should("exist");
    
    // Wait for the header/toolbar
    cy.get(".edit-post-header", { timeout: 30000 }).should("be.visible");
  };

  beforeEach(() => {
    visitPostEditor(); 
  });

// -------------------------------------------------------------------

  describe('Page Load Functionality', () => {
    it('should successfully load the post editor page', () => {
      cy.url().should('include', '/wp-admin/post-new.php');

      // Check that the editor UI really loaded via a stable control:
      cy.contains('button', 'Publish', { timeout: 30000 }).should('be.visible');
    });
  });

// -------------------------------------------------------------------

  describe('Title Input Functionality', () => {
    it('should allow user to type and save a post title', () => {
      const testTitle = 'My Functional Test Post';
      
      // 💡 FINAL FIX 3: Wait right before typing
      getTitleInput().wait(100).type(testTitle, { delay: 50, force: true }); 

      cy.contains(testTitle).should('be.visible');
    });

    it('should preserve title text after typing', () => {
      const title = 'Title Preservation Test';

      getTitleInput().wait(100).type(title, { delay: 50, force: true });

      // Click elsewhere (e.g., in the header)
      cy.get('.edit-post-header').click();

      cy.contains(title).should('be.visible');
    });

    it('should handle special characters in title', () => {
      const specialTitle = 'Test & Title with "Quotes" and \'Apostrophes\'';

      getTitleInput().wait(100).type(specialTitle, { delay: 50, force: true });

      cy.contains('Test & Title').should('be.visible');
      cy.contains('Quotes').should('be.visible');
    });
  });

// -------------------------------------------------------------------

  describe('Content Editor Functionality', () => {
    it('should allow user to add and display content', () => {
      const testContent = 'This is my test content for the post.';

      getEditorArea().click().wait(100).type(testContent, { delay: 50, force: true }); 

      cy.contains(testContent).should('be.visible');
    });

    it('should create multiple paragraphs when pressing Enter', () => {
      getEditorArea().click();

      cy.focused().wait(100).type('First paragraph content', { delay: 50, force: true });
      cy.focused().type('{enter}', { delay: 50, force: true }); 
      cy.focused().wait(100).type('Second paragraph content', { delay: 50, force: true });

      cy.contains('First paragraph content').should('be.visible');
      cy.contains('Second paragraph content').should('be.visible');
    });

    it('should maintain content when clicking away and back', () => {
      const content = 'Persistent content test';

      getEditorArea().click().wait(100).type(content, { delay: 50, force: true });

      getTitleInput().click();
      cy.contains(content).should('be.visible');
    });
  });

// -------------------------------------------------------------------

  describe('Save Draft Functionality', () => {
    it('should save a post as draft with title and content', () => {
      const postTitle = 'Draft Functionality Test';
      const postContent = 'This is the content for draft test.';

      // Add title
      getTitleInput().wait(100).type(postTitle, { delay: 50, force: true });

      // Add content
      getEditorArea().click().wait(100).type(postContent, { delay: 50, force: true });

      // Save draft (Using the robust helper)
      getSaveDraftButton().click({ force: true });
      cy.wait(3000);

      cy.contains('Draft saved').should('be.visible');
      cy.url().should('include', 'post.php');
    });

    it('should preserve data after saving draft', () => {
      const title = 'Save and Preserve Test';
      const content = 'Content that should persist after save';

      // Create post
      getTitleInput().wait(100).type(title, { delay: 50, force: true });
      getEditorArea().click().wait(100).type(content, { delay: 50, force: true });

      // Save
      getSaveDraftButton().click({ force: true });
      cy.wait(3000);

      // Verify data is still there
      cy.contains(title).should('be.visible');
      cy.contains(content).should('be.visible');
    });
  });

// -------------------------------------------------------------------

  describe('Complete Post Creation Workflow', () => {
    it('should successfully create a complete post from start to finish', () => {
      const postTitle = 'Complete Workflow Test Post';
      const postContent = 'This is the complete content for the workflow test. It includes multiple sentences to make it more realistic.';

      // Step 1: Add title
      getTitleInput().wait(100).type(postTitle, { delay: 50, force: true });

      // Step 2: Add content
      getEditorArea().click().wait(100).type(postContent, { delay: 30, force: true });

      // Step 3: Save draft
      getSaveDraftButton().click({ force: true });
      cy.wait(3000);

      // Step 4: Verify everything is saved
      cy.url().should('include', 'post.php');
      cy.contains(postTitle).should('be.visible');
      cy.contains(postContent).should('be.visible');
    });

    it('should allow editing after saving', () => {
      const originalTitle = 'Original Title';
      const updatedTitle = 'Updated Title After Save';

      // Create and save
      getTitleInput().wait(100).type(originalTitle, { delay: 50, force: true });

      getSaveDraftButton().click({ force: true });
      cy.wait(3000);

      // Edit the title: Select all existing text and type the new title
      getTitleInput().wait(100).type('{selectall}' + updatedTitle, { delay: 50, force: true });

      // Save again
      getSaveDraftButton().click({ force: true });
      cy.wait(3000);

      // Verify update
      cy.contains(updatedTitle).should('be.visible');
      cy.contains(originalTitle).should('not.exist');
    });
  });

// -------------------------------------------------------------------

  describe('Content Persistence After Reload', () => {
    it('should preserve post data after page reload', () => {
      const title = 'Reload Persistence Test';
      const content = 'This content should survive a reload';

      // Create post
      getTitleInput().wait(100).type(title, { delay: 50, force: true });
      getEditorArea().click().wait(100).type(content, { delay: 50, force: true });

      // Save
      getSaveDraftButton().click({ force: true });
      cy.wait(3000);

      // Get the URL (which now includes the post ID)
      cy.url().then((url) => {
        // Reload the page
        cy.visit(url);
        
        // Wait for the editor to stabilize after reload
        cy.get(".edit-post-header", { timeout: 30000 }).should("be.visible");
        cy.wait(3000);

        // Verify data persists after reload
        cy.contains(title).should('be.visible');
        cy.contains(content, { timeout: 10000 }).should('be.visible');
      });
    });
  });

// -------------------------------------------------------------------

  describe('Multiple Content Blocks Functionality', () => {
    it('should handle multiple paragraphs of content', () => {
      getEditorArea().click();

      const paragraphs = [
        'First paragraph with some content',
        'Second paragraph with different content',
        'Third paragraph to complete the test'
      ];

      paragraphs.forEach((para, index) => {
        // Only press Enter after the first paragraph to start a new block
        if (index > 0) {
          cy.focused().type('{enter}', { delay: 50, force: true });
        }
        cy.focused().wait(100).type(para, { delay: 40, force: true });
      });

      // Verify all paragraphs are visible
      paragraphs.forEach(para => {
        cy.contains(para).should('be.visible');
      });
    });
  });

// -------------------------------------------------------------------

  describe('Text Editing Functionality', () => {
    it('should allow deleting text with backspace', () => {
      const initialText = 'Text to be deleted';

      getEditorArea().click().wait(100).type(initialText, { delay: 50, force: true });

      // Delete 8 characters
      cy.focused().type('{backspace}{backspace}{backspace}{backspace}{backspace}{backspace}{backspace}{backspace}', { force: true });

      cy.contains('Text to be').should('be.visible');
      cy.contains('deleted').should('not.exist');
    });

    it('should allow selecting and replacing text', () => {
      getTitleInput().wait(100).type('Original Text', { delay: 50, force: true });

      // Select all and replace
      getTitleInput().type('{selectall}New Text', { delay: 50, force: true });

      cy.contains('New Text').should('be.visible');
      cy.contains('Original Text').should('not.exist');
    });
  });
});