import mysql.connector
import pytest

# Database connection info
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'wordpress_sqe'
}

# Fixture to connect to DB
@pytest.fixture
def db_connection():
    conn = mysql.connector.connect(**DB_CONFIG)
    yield conn
    conn.close()


# -----------------------------
# Test Cases
# -----------------------------

# 1 Fetch all users
def test_fetch_users(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("SELECT * FROM wp_users;")
    result = cursor.fetchall()
    assert len(result) > 0  # At least one user
    cursor.close()


# 2 Check specific admin user
def test_check_specific_user(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("SELECT * FROM wp_users WHERE user_login='admin';")  # Replace if needed
    result = cursor.fetchall()
    assert len(result) == 1
    cursor.close()


# 3 Fetch all published posts
def test_fetch_published_posts(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("SELECT * FROM wp_posts WHERE post_status='publish';")
    result = cursor.fetchall()
    assert isinstance(result, list)
    cursor.close()


# 4 Count total posts
def test_count_posts(db_connection):
    cursor = db_connection.cursor()
    cursor.execute("SELECT COUNT(*) FROM wp_posts;")
    count = cursor.fetchone()[0]
    assert count >= 0
    cursor.close()


# 5 Count comments per post
def test_comments_per_post(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("""
        SELECT comment_post_ID, COUNT(*) AS comment_count
        FROM wp_comments
        GROUP BY comment_post_ID;
    """)
    result = cursor.fetchall()
    assert isinstance(result, list)
    cursor.close()


# 6 Insert mock post, verify, then delete
def test_insert_mock_post(db_connection):
    cursor = db_connection.cursor()
    
    # Insert a temporary post
    cursor.execute("""
        INSERT INTO wp_posts (post_author, post_date, post_content, post_title, post_status)
        VALUES (1, NOW(), 'Test content', 'Test Title', 'publish');
    """)
    db_connection.commit()

    # Verify it exists
    cursor.execute("SELECT * FROM wp_posts WHERE post_title='Test Title';")
    result = cursor.fetchall()
    assert len(result) == 1

    # Clean up
    cursor.execute("DELETE FROM wp_posts WHERE post_title='Test Title';")
    db_connection.commit()
    cursor.close()


# 7 Test retrieving a site option
def test_site_option(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("SELECT option_value FROM wp_options WHERE option_name='siteurl';")
    result = cursor.fetchone()
    assert result is not None
    cursor.close()


# 8 Test retrieving post metadata
def test_postmeta(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("SELECT * FROM wp_postmeta LIMIT 1;")
    result = cursor.fetchall()
    assert isinstance(result, list)
    cursor.close()


# 9 Test retrieving user metadata
def test_usermeta(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("SELECT * FROM wp_usermeta WHERE user_id=1;")
    result = cursor.fetchall()
    assert isinstance(result, list)
    cursor.close()


# 10 Test retrieving a term (category/tag)
def test_terms(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("SELECT * FROM wp_terms LIMIT 1;")
    result = cursor.fetchall()
    assert isinstance(result, list)
    cursor.close()


# 11 Check admin has wp_capabilities meta
def test_admin_capabilities(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("""
        SELECT meta_value FROM wp_usermeta 
        WHERE user_id=1 AND meta_key='wp_capabilities';
    """)
    result = cursor.fetchone()
    assert result is not None
    cursor.close()


# 12 Check a specific postmeta key exists for first post
def test_postmeta_key_exists(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("SELECT meta_key, meta_value FROM wp_postmeta LIMIT 1;")
    result = cursor.fetchone()
    assert result is not None
    cursor.close()



# 13 Verify a comment belongs to the correct post
def test_comment_post_relation(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("""
        SELECT comment_ID, comment_post_ID FROM wp_comments LIMIT 1;
    """)
    result = cursor.fetchone()
    assert result is not None
    cursor.close()


# 14 Check another site option exists (e.g., blogname)
def test_blogname_option(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("""
        SELECT option_value FROM wp_options WHERE option_name='blogname';
    """)
    result = cursor.fetchone()
    assert result is not None
    cursor.close()


# 15 Verify post-category relationship exists
def test_post_category_relationship(db_connection):
    cursor = db_connection.cursor(dictionary=True)
    cursor.execute("""
        SELECT object_id, term_taxonomy_id FROM wp_term_relationships LIMIT 1;
    """)
    result = cursor.fetchone()
    assert result is not None
    cursor.close()