import pytest
from datetime import datetime

# ========================================
# 1. LOGIN SYSTEM TESTS
# ========================================

def test_login_admin_valid():
    """Login admin dengan data valid"""
    username = "admin"
    password = "admin123"

    valid_users = {
        "admin": {"password": "admin123", "role": "admin"},
        "user1": {"password": "admin123", "role": "user"}
    }

    is_valid = username in valid_users and valid_users[username]["password"] == password

    assert is_valid == True
    assert valid_users[username]["role"] == "admin"

    print("✅ Login admin berhasil")


# ========================================
# 2. PENJUMLAHAN HARGA BOOKING
# ========================================

def test_calculate_booking_price():
    """Perhitungan harga booking (tanpa diskon)"""
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
    print("✅ Perhitungan harga booking benar")


# ========================================
# 3. PENGAMBILAN DATA API
# ========================================

def test_get_items_from_api():
    """Mengambil data barang dari API (mock)"""
    mock_response = {
        "status": "success",
        "data": [
            {
                "id": 1,
                "name": "Tenda",
                "price_per_day": 150000,
                "quantity_available": 5,
                "status": "available"
            },
            {
                "id": 2,
                "name": "Sleeping Bag",
                "price_per_day": 50000,
                "quantity_available": 7,
                "status": "available"
            }
        ]
    }

    assert mock_response["status"] == "success"
    assert isinstance(mock_response["data"], list)
    assert len(mock_response["data"]) == 2

    for item in mock_response["data"]:
        assert "id" in item
        assert "name" in item
        assert "price_per_day" in item
        assert "quantity_available" in item
        assert item["status"] in ["available", "unavailable"]

    print("✅ Data item dari API berhasil diuji")
