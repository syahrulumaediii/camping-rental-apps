import pytest

# ================================
# FUNGSI-FUNGSI YANG AKAN DITEST
# ================================

def is_even(n):
    """
    Fungsi untuk mengecek apakah angka genap
    Args:
        n (int): angka yang akan dicek
    Returns:
        bool: True jika genap, False jika ganjil
    """
    return n % 2 == 0


def is_palindrome(s):
    """
    Fungsi untuk mengecek apakah string adalah palindrome
    Args:
        s (str): string yang akan dicek
    Returns:
        bool: True jika palindrome, False jika tidak
    """
    # Bersihkan string dari spasi dan ubah ke lowercase
    cleaned = s.replace(" ", "").lower()
    return cleaned == cleaned[::-1]


# ================================
# TEST CASE 1: IS_EVEN - Angka Genap
# ================================
def test_is_even_with_even_number():
    """Test is_even dengan angka genap"""
    assert is_even(4) == True
    assert is_even(10) == True
    assert is_even(0) == True
    print("\n✓ Test angka genap (4, 10, 0): PASSED")
    
# ================================
# TEST CASE 2: IS_EVEN - Angka Ganjil
# ================================
def test_is_even_with_odd_number():
    """Test is_even dengan angka ganjil"""
    assert is_even(3) == False
    assert is_even(7) == False
    assert is_even(99) == False
    print("\n✓ Test angka ganjil (3, 7, 99): PASSED")

# ================================
# TEST CASE 3: IS_PALINDROME - Palindrome
# ================================
def test_is_palindrome_with_palindrome_text():
    """Test is_palindrome dengan teks palindrome"""
    assert is_palindrome("katak") == True
    assert is_palindrome("kodok") == True
    assert is_palindrome("malam") == True
    print("\n✓ Test palindrome (katak, kodok, malam): PASSED")

# ================================
# TEST CASE 4: IS_PALINDROME - Dengan Spasi
# ================================
def test_is_palindrome_with_spaces():
    """Test is_palindrome dengan spasi"""
    assert is_palindrome("kasur rusak") == True
    assert is_palindrome("ibu ratna antar ubi") == True
    print("\n✓ Test palindrome dengan spasi: PASSED")