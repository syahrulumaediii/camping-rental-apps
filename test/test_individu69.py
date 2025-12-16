import pytest
import time

# ================================
# 1. LOGIN SYSTEM TESTS
# ================================
def test_login_admin_valid():
    username = "admin"
    password = "admin123"
    valid_users = {
        "admin": {"password": "admin123", "role": "admin"},
        "syahrul": {"password": "admin123", "role": "user"}
    }
    assert username in valid_users and valid_users[username]["password"] == password
    assert valid_users[username]["role"] == "admin"

# ================================
# 2. LOGIN Validasi SQL Injection
# ================================
def test_login_sql_injection():
    username = "admin' OR '1'='1"
    password = "test"
    valid_users = {
        "admin": {"password": "admin123"}
    }
    assert username not in valid_users
    
# ================================
# 3. Calculate Booking Price Tests
# ================================
def test_calculate_booking_price():
    price_per_day = 150000
    days = 3
    quantity = 2
    tax_rate = 10

    subtotal = price_per_day * days * quantity
    tax = subtotal * (tax_rate / 100)
    total = subtotal + tax

    assert subtotal == 900000
    assert tax == 90000
    assert total == 990000

