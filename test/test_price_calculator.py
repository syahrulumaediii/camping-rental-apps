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


# def test_calculate_booking_price_with_discount():
#     price_per_day = 100000
#     days = 7
#     quantity = 1
#     discount_rate = 5
#     tax_rate = 10

#     subtotal = price_per_day * days * quantity
#     discount = subtotal * (discount_rate / 100) if days > 5 else 0
#     subtotal_after_discount = subtotal - discount
#     tax = subtotal_after_discount * (tax_rate / 100)
#     total = subtotal_after_discount + tax

#     assert subtotal == 700000
#     assert discount == 35000
#     assert subtotal_after_discount == 665000
#     assert tax == 66500
#     assert total == 731500
