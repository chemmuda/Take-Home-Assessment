import 'package:flutter_test/flutter_test.dart';
import 'package:qa_assessment_app/models/order.dart';

void main() {
  group('OrderItem Model', () {
    test('should create OrderItem from JSON', () {
      final json = {
        'id': 1,
        'product_id': 10,
        'quantity': 2,
        'price': 99.99,
      };

      final orderItem = OrderItem.fromJson(json);

      expect(orderItem.id, 1);
      expect(orderItem.productId, 10);
      expect(orderItem.quantity, 2);
      expect(orderItem.price, 99.99);
    });

    test('should handle null price in OrderItem', () {
      final json = {
        'id': 2,
        'product_id': 20,
        'quantity': 1,
        'price': null,
      };

      final orderItem = OrderItem.fromJson(json);

      expect(orderItem.price, 0.0);
    });

    test('should convert OrderItem to JSON', () {
      final orderItem = OrderItem(
        id: 3,
        productId: 30,
        quantity: 5,
        price: 25.50,
      );

      final json = orderItem.toJson();

      expect(json['id'], 3);
      expect(json['product_id'], 30);
      expect(json['quantity'], 5);
      expect(json['price'], 25.50);
    });
  });

  group('Order Model', () {
    test('should create Order from JSON without items', () {
      final json = {
        'id': 100,
        'user_id': 1,
        'status': 'pending',
        'total_amount': 199.99,
      };

      final order = Order.fromJson(json);

      expect(order.id, 100);
      expect(order.userId, 1);
      expect(order.status, 'pending');
      expect(order.totalAmount, 199.99);
      expect(order.items, isNull);
    });

    test('should create Order from JSON with items', () {
      final json = {
        'id': 101,
        'user_id': 2,
        'status': 'completed',
        'total_amount': 299.98,
        'items': [
          {
            'id': 1,
            'product_id': 10,
            'quantity': 2,
            'price': 99.99,
          },
          {
            'id': 2,
            'product_id': 20,
            'quantity': 1,
            'price': 100.0,
          },
        ],
      };

      final order = Order.fromJson(json);

      expect(order.id, 101);
      expect(order.userId, 2);
      expect(order.status, 'completed');
      expect(order.totalAmount, 299.98);
      expect(order.items, isNotNull);
      expect(order.items!.length, 2);
      expect(order.items![0].productId, 10);
      expect(order.items![1].productId, 20);
    });

    test('should handle null total_amount', () {
      final json = {
        'id': 102,
        'user_id': 3,
        'status': 'cancelled',
        'total_amount': null,
      };

      final order = Order.fromJson(json);

      expect(order.totalAmount, 0.0);
    });

    test('should convert Order to JSON without items', () {
      final order = Order(
        id: 103,
        userId: 4,
        status: 'shipped',
        totalAmount: 499.99,
      );

      final json = order.toJson();

      expect(json['id'], 103);
      expect(json['user_id'], 4);
      expect(json['status'], 'shipped');
      expect(json['total_amount'], 499.99);
      expect(json['items'], isNull);
    });

    test('should convert Order to JSON with items', () {
      final order = Order(
        id: 104,
        userId: 5,
        status: 'delivered',
        totalAmount: 599.99,
        items: [
          OrderItem(id: 1, productId: 10, quantity: 3, price: 199.99),
          OrderItem(id: 2, productId: 20, quantity: 1, price: 200.0),
        ],
      );

      final json = order.toJson();

      expect(json['id'], 104);
      expect(json['user_id'], 5);
      expect(json['status'], 'delivered');
      expect(json['total_amount'], 599.99);
      expect(json['items'], isNotNull);
      expect(json['items'].length, 2);
    });
  });
}
