import pytest
import requests
import time
import base64

# ------------------------
# CONFIGURATION
# ------------------------
BASE_URL = "https://localhost/WordPress_sqe"  # your folder
API_ENDPOINT = f"{BASE_URL}/wp-json/wp/v2"
TEST_USERNAME = "admin"                       # your admin username
TEST_PASSWORD = "aZZY TYWm RoRw rDao npEV RJYM"                    # your admin password


# ------------------------
# HELPER FUNCTIONS
# ------------------------
def get_auth_header():
    """Generate Basic Auth header for WordPress"""
    credentials = f"{TEST_USERNAME}:{TEST_PASSWORD}"
    encoded = base64.b64encode(credentials.encode()).decode()
    return {"Authorization": f"Basic {encoded}"}

# Test connection first
def test_connection():
    """Verify WordPress is accessible"""
    response = requests.get(f"{BASE_URL}/wp-json", verify=False)
    assert response.status_code == 200, f"WordPress not accessible at {BASE_URL}"

# ------------------------
# FIXTURES
# ------------------------
@pytest.fixture
def auth_session():
    """Create session with proper auth headers"""
    session = requests.Session()
    session.headers.update(get_auth_header())
    session.verify = False  # ignore self-signed SSL
    yield session
    session.close()

# ------------------------
# POSTS API TESTS
# ------------------------
class TestWordPressPostsAPI:

    @pytest.fixture(autouse=True)
    def setup(self, auth_session):
        self.session = auth_session
        self.created_post_ids = []
        yield
        # Cleanup created posts
        for post_id in self.created_post_ids:
            try:
                self.session.delete(f"{API_ENDPOINT}/posts/{post_id}?force=true")
            except:
                pass

    def test_get_all_posts(self):
        response = self.session.get(f"{API_ENDPOINT}/posts")
        print(f"\nGET /posts - Status: {response.status_code}")
        if response.status_code != 200:
            print(f"Response: {response.text}")
        assert response.status_code == 200
        assert isinstance(response.json(), list)

    def test_create_post(self):
        post_data = {
            "title": f"Test Post {int(time.time())}",
            "content": "This is a test post content",
            "status": "draft"
        }
        response = self.session.post(f"{API_ENDPOINT}/posts", json=post_data)
        print(f"\nPOST /posts - Status: {response.status_code}")
        if response.status_code != 201:
            print(f"Response: {response.text}")
        assert response.status_code == 201
        data = response.json()
        self.created_post_ids.append(data["id"])
        assert data["title"]["rendered"] == post_data["title"]

    def test_get_post_by_id(self):
        post_data = {
            "title": f"Get Post Test {int(time.time())}",
            "content": "Test content",
            "status": "draft"
        }
        create_resp = self.session.post(f"{API_ENDPOINT}/posts", json=post_data)
        if create_resp.status_code == 201:
            post_id = create_resp.json()["id"]
            self.created_post_ids.append(post_id)
            response = self.session.get(f"{API_ENDPOINT}/posts/{post_id}")
            assert response.status_code == 200
            assert response.json()["id"] == post_id

    def test_update_post(self):
        post_data = {"title": "Original", "content": "Original content", "status": "draft"}
        create_resp = self.session.post(f"{API_ENDPOINT}/posts", json=post_data)
        if create_resp.status_code != 201:
            pytest.skip("Could not create post")
        post_id = create_resp.json()["id"]
        self.created_post_ids.append(post_id)
        update_data = {"title": "Updated", "content": "Updated content"}
        response = self.session.post(f"{API_ENDPOINT}/posts/{post_id}", json=update_data)
        assert response.status_code == 200
        assert response.json()["title"]["rendered"] == "Updated"

    def test_delete_post(self):
        post_data = {"title": "Delete", "content": "Delete me", "status": "draft"}
        create_resp = self.session.post(f"{API_ENDPOINT}/posts", json=post_data)
        if create_resp.status_code == 201:
            post_id = create_resp.json()["id"]
            response = self.session.delete(f"{API_ENDPOINT}/posts/{post_id}?force=true")
            assert response.status_code == 200

    def test_publish_post(self):
        post_data = {"title": "Draft to Publish", "content": "Publishing this", "status": "draft"}
        create_resp = self.session.post(f"{API_ENDPOINT}/posts", json=post_data)
        if create_resp.status_code == 201:
            post_id = create_resp.json()["id"]
            self.created_post_ids.append(post_id)
            update_data = {"status": "publish"}
            response = self.session.post(f"{API_ENDPOINT}/posts/{post_id}", json=update_data)
            assert response.status_code == 200
            assert response.json()["status"] == "publish"

# ------------------------
# USERS API TESTS
# ------------------------
class TestWordPressUsersAPI:

    @pytest.fixture(autouse=True)
    def setup(self, auth_session):
        self.session = auth_session
        self.created_user_ids = []
        yield
        for user_id in self.created_user_ids:
            try:
                self.session.delete(f"{API_ENDPOINT}/users/{user_id}?reassign=1&force=true")
            except:
                pass

    def test_get_current_user(self):
        response = self.session.get(f"{API_ENDPOINT}/users/me")
        print(f"\nGET /users/me - Status: {response.status_code}")
        assert response.status_code == 200

    def test_create_user(self):
        timestamp = int(time.time())
        user_data = {
            "username": f"user{timestamp}",
            "email": f"user{timestamp}@example.com",
            "password": "SecurePass123!",
            "name": "Test User"
        }
        response = self.session.post(f"{API_ENDPOINT}/users", json=user_data)
        print(f"\nPOST /users - Status: {response.status_code}")
        assert response.status_code == 201
        self.created_user_ids.append(response.json()["id"])

    def test_get_all_users(self):
        response = self.session.get(f"{API_ENDPOINT}/users")
        assert response.status_code == 200
        assert isinstance(response.json(), list)

    def test_get_user_by_id(self):
        response = self.session.get(f"{API_ENDPOINT}/users/1")
        assert response.status_code == 200

# ------------------------
# AUTHENTICATION TESTS
# ------------------------
class TestWordPressAuthenticationAPI:

    def test_auth_failure_invalid_credentials(self):
        bad_credentials = base64.b64encode(b"fakeuser:wrongpass").decode()
        headers = {"Authorization": f"Basic {bad_credentials}"}
        response = requests.get(f"{API_ENDPOINT}/users/me", headers=headers, verify=False)
        assert response.status_code == 401

    def test_auth_success_valid_credentials(self):
        headers = get_auth_header()
        response = requests.get(f"{API_ENDPOINT}/users/me", headers=headers, verify=False)
        assert response.status_code == 200

    def test_no_auth_endpoint(self):
        response = requests.get(f"{API_ENDPOINT}/posts", verify=False)
        assert response.status_code == 200

# ------------------------
# ERROR HANDLING TESTS
# ------------------------
class TestWordPressErrorHandling:

    @pytest.fixture(autouse=True)
    def setup(self, auth_session):
        self.session = auth_session

    def test_404_invalid_endpoint(self):
        response = self.session.get(f"{API_ENDPOINT}/invalid-endpoint")
        assert response.status_code == 404

    def test_404_nonexistent_post(self):
        response = self.session.get(f"{API_ENDPOINT}/posts/999999")
        assert response.status_code == 404

    def test_response_headers(self):
        response = self.session.get(f"{API_ENDPOINT}/posts")
        assert "content-type" in response.headers
        assert "application/json" in response.headers.get("content-type", "")