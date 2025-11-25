import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:qa_assessment_app/models/product.dart';
import 'package:qa_assessment_app/models/user.dart';
import 'package:qa_assessment_app/screens/product_detail_screen.dart';

void main() {
  group('ProductDetailScreen Widget', () {
    final testUser = User(
      id: 1,
      name: 'Test User',
      email: 'test@example.com',
    );

    testWidgets('should display product name in app bar', (tester) async {
      final product = Product(
        id: 1,
        name: 'Test Product',
        price: 99.99,
        stock: 10,
      );

      await tester.pumpWidget(
        MaterialApp(
          home: ProductDetailScreen(
            product: product,
            user: testUser,
          ),
        ),
      );

      expect(
        find.descendant(
          of: find.byType(AppBar),
          matching: find.text('Test Product'),
        ),
        findsOneWidget,
      );
    });

    testWidgets('should display product details', (tester) async {
      final product = Product(
        id: 2,
        name: 'Laptop',
        description: 'High-performance laptop',
        price: 1299.99,
        stock: 5,
      );

      await tester.pumpWidget(
        MaterialApp(
          home: ProductDetailScreen(
            product: product,
            user: testUser,
          ),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.text('Laptop'), findsWidgets);
      expect(find.text('\$1299.99'), findsOneWidget);
      expect(find.text('High-performance laptop'), findsOneWidget);
      expect(find.text('Description'), findsOneWidget);
    });

    testWidgets('should show Add to Cart button when in stock',
        (tester) async {
      final product = Product(
        id: 3,
        name: 'Mouse',
        price: 29.99,
        stock: 15,
      );

      await tester.pumpWidget(
        MaterialApp(
          home: ProductDetailScreen(
            product: product,
            user: testUser,
          ),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.widgetWithText(ElevatedButton, 'Add to Cart'),
          findsOneWidget);
    });

    testWidgets('should show Out of Stock button when stock is zero',
        (tester) async {
      final product = Product(
        id: 4,
        name: 'Keyboard',
        price: 79.99,
        stock: 0,
      );

      await tester.pumpWidget(
        MaterialApp(
          home: ProductDetailScreen(
            product: product,
            user: testUser,
          ),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.widgetWithText(ElevatedButton, 'Out of Stock'),
          findsOneWidget);
    });

    testWidgets('should disable button when out of stock', (tester) async {
      final product = Product(
        id: 5,
        name: 'Monitor',
        price: 399.99,
        stock: 0,
      );

      await tester.pumpWidget(
        MaterialApp(
          home: ProductDetailScreen(
            product: product,
            user: testUser,
          ),
        ),
      );

      await tester.pumpAndSettle();

      final button = tester.widget<ElevatedButton>(
        find.widgetWithText(ElevatedButton, 'Out of Stock'),
      );

      expect(button.onPressed, isNull);
    });

    testWidgets('should not display description section if null',
        (tester) async {
      final product = Product(
        id: 6,
        name: 'Headphones',
        price: 149.99,
        stock: 20,
      );

      await tester.pumpWidget(
        MaterialApp(
          home: ProductDetailScreen(
            product: product,
            user: testUser,
          ),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.text('Description'), findsNothing);
    });

    testWidgets('should show snackbar when Add to Cart is tapped',
        (tester) async {
      final product = Product(
        id: 7,
        name: 'Tablet',
        price: 599.99,
        stock: 8,
      );

      await tester.pumpWidget(
        MaterialApp(
          home: ProductDetailScreen(
            product: product,
            user: testUser,
          ),
        ),
      );

      await tester.pumpAndSettle();

      await tester.tap(find.widgetWithText(ElevatedButton, 'Add to Cart'));
      await tester.pumpAndSettle();

      expect(find.text('Feature not implemented'), findsOneWidget);
    });
  });
}
