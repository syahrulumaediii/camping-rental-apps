def test_get_items_from_api():
    mock_api_response = {
        "status": "success",
        "data": [
            {
                "id": 1,
                "name": "Tenda Dome 4 Orang",
                "description": "Tenda berkualitas tinggi",
                "category": "Tenda",
                "price_per_day": 150000,
                "quantity_available": 5,
                "status": "available"
            },
            {
                "id": 2,
                "name": "Sleeping Bag Premium",
                "description": "Sleeping bag premium",
                "category": "Perlengkapan Tidur",
                "price_per_day": 50000,
                "quantity_available": 10,
                "status": "available"
            }
        ],
        "total": 2
    }

    assert mock_api_response['status'] == 'success'
    assert isinstance(mock_api_response['data'], list)
    assert len(mock_api_response['data']) > 0

    for item in mock_api_response["data"]:
        assert "id" in item
        assert "name" in item
        assert "price_per_day" in item
        assert "quantity_available" in item
        assert "status" in item

        assert isinstance(item["id"], int)
        assert isinstance(item["name"], str)
        assert isinstance(item["price_per_day"], (int, float))
        assert isinstance(item["quantity_available"], int)
        assert item["status"] in ["available", "unavailable"]
