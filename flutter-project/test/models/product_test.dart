import 'package:flutter_test/flutter_test.dart';
import 'package:qa_assessment_app/models/product.dart';

void main() {
  group('Product Model', () {
    test('should create Product from JSON', () {
      final json = {
        'id': 1,
        'name': 'Laptop',
        'description': 'High-performance laptop',
        'price': 999.99,
        'stock': 10,
        'category': 'Electronics',
      };

      final product = Product.fromJson(json);

      expect(product.id, 1);
      expect(product.name, 'Laptop');
      expect(product.description, 'High-performance laptop');
      expect(product.price, 999.99);
      expect(product.stock, 10);
      expect(product.category, 'Electronics');
    });

    test('should create Product from JSON with missing optional fields', () {
      final json = {
        'id': 2,
        'name': 'Mouse',
        'price': 25,
        'stock': 0,
      };

      final product = Product.fromJson(json);

      expect(product.id, 2);
      expect(product.name, 'Mouse');
      expect(product.description, isNull);
      expect(product.price, 25.0);
      expect(product.stock, 0);
      expect(product.category, isNull);
    });

    test('should handle null price gracefully', () {
      final json = {
        'id': 3,
        'name': 'Keyboard',
        'price': null,
        'stock': 5,
      };

      final product = Product.fromJson(json);

      expect(product.price, 0.0);
    });

    test('should handle missing stock field', () {
      final json = {
        'id': 4,
        'name': 'Monitor',
        'price': 299.99,
      };

      final product = Product.fromJson(json);

      expect(product.stock, 0);
    });

    test('should convert Product to JSON', () {
      final product = Product(
        id: 5,
        name: 'Headphones',
        description: 'Wireless headphones',
        price: 149.99,
        stock: 20,
        category: 'Audio',
      );

      final json = product.toJson();

      expect(json['id'], 5);
      expect(json['name'], 'Headphones');
      expect(json['description'], 'Wireless headphones');
      expect(json['price'], 149.99);
      expect(json['stock'], 20);
      expect(json['category'], 'Audio');
    });

    test('isInStock should return true when stock > 0', () {
      final product = Product(
        id: 6,
        name: 'Speaker',
        price: 79.99,
        stock: 5,
      );

      expect(product.isInStock, true);
    });

    test('isInStock should return false when stock = 0', () {
      final product = Product(
        id: 7,
        name: 'Tablet',
        price: 399.99,
        stock: 0,
      );

      expect(product.isInStock, false);
    });

    test('isInStock should return false when stock is negative', () {
      final product = Product(
        id: 8,
        name: 'Phone',
        price: 699.99,
        stock: -1,
      );

      expect(product.isInStock, false);
    });
  });
}
