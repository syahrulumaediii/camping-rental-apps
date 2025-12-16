from datetime import datetime

def test_get_user_bookings_from_api():
    user_id = 2

    mock_bookings_response = {
        "status": "success",
        "data": [
            {
                "id": 1,
                "user_id": 2,
                "item_id": 1,
                "item_name": "Tenda Dome 4 Orang",
                "start_date": "2024-12-10",
                "end_date": "2024-12-13",
                "quantity": 2,
                "total_price": 900000,
                "status": "pending"
            },
            {
                "id": 2,
                "user_id": 2,
                "item_id": 2,
                "item_name": "Sleeping Bag Premium",
                "start_date": "2024-12-15",
                "end_date": "2024-12-17",
                "quantity": 1,
                "total_price": 100000,
                "status": "confirmed"
            }
        ],
        "total": 2
    }

    assert mock_bookings_response['status'] == 'success'
    assert isinstance(mock_bookings_response["data"], list)

    for booking in mock_bookings_response["data"]:
        required_fields = ["id", "user_id", "item_id", "item_name",
                           "start_date", "end_date", "quantity", "total_price", "status"]

        for field in required_fields:
            assert field in booking

        assert booking["user_id"] == user_id
        assert booking["status"] in ["pending", "confirmed", "completed", "cancelled"]

        start_date = datetime.strptime(booking["start_date"], "%Y-%m-%d")
        end_date = datetime.strptime(booking["end_date"], "%Y-%m-%d")

        assert end_date >= start_date
        assert booking["total_price"] > 0
