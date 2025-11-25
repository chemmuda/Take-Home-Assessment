import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:qa_assessment_app/models/user.dart';
import 'package:qa_assessment_app/models/product.dart';
import 'package:qa_assessment_app/screens/products_screen.dart';
import 'package:qa_assessment_app/screens/product_detail_screen.dart';
import 'package:qa_assessment_app/screens/orders_screen.dart';
import 'package:qa_assessment_app/screens/profile_screen.dart';

void main() {
  group('Navigation Integration Tests', () {
    final testUser = User(
      id: 1,
      name: 'Test User',
      email: 'test@example.com',
    );

    final testProduct = Product(
      id: 1,
      name: 'Test Product',
      description: 'Test Description',
      price: 99.99,
      stock: 10,
    );

    testWidgets('should navigate from products to orders screen',
        (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: ProductsScreen(user: testUser),
        ),
      );

      // Wait for initial load
      await tester.pump();

      // Tap shopping cart icon
      await tester.tap(find.byIcon(Icons.shopping_cart));
      await tester.pumpAndSettle();

      // Verify we're on orders screen
      expect(find.text('My Orders'), findsOneWidget);
    });

    testWidgets('should navigate from products to profile screen',
        (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: ProductsScreen(user: testUser),
        ),
      );

      await tester.pump();

      // Tap person icon
      await tester.tap(find.byIcon(Icons.person));
      await tester.pumpAndSettle();

      // Verify we're on profile screen
      expect(find.text('Profile'), findsOneWidget);
    });

    testWidgets('should navigate back from orders screen', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: ProductsScreen(user: testUser),
        ),
      );

      await tester.pump();

      // Navigate to orders
      await tester.tap(find.byIcon(Icons.shopping_cart));
      await tester.pumpAndSettle();

      expect(find.text('My Orders'), findsOneWidget);

      // Navigate back
      await tester.tap(find.byType(BackButton));
      await tester.pumpAndSettle();

      // Should be back on products screen
      expect(find.text('Products'), findsOneWidget);
    });

    testWidgets('should navigate back from profile screen', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: ProductsScreen(user: testUser),
        ),
      );

      await tester.pump();

      // Navigate to profile
      await tester.tap(find.byIcon(Icons.person));
      await tester.pumpAndSettle();

      expect(find.text('Profile'), findsOneWidget);

      // Navigate back
      await tester.tap(find.byType(BackButton));
      await tester.pumpAndSettle();

      // Should be back on products screen
      expect(find.text('Products'), findsOneWidget);
    });

    testWidgets('should navigate to product detail screen', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: ProductDetailScreen(
            product: testProduct,
            user: testUser,
          ),
        ),
      );

      await tester.pumpAndSettle();

      // Verify product detail screen elements
      expect(find.text('Test Product'), findsAtLeastNWidgets(1));
      expect(find.text('\$99.99'), findsOneWidget);
      expect(find.text('Test Description'), findsOneWidget);
    });

    testWidgets('product detail should have back navigation', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: Builder(
              builder: (context) => ElevatedButton(
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => ProductDetailScreen(
                        product: testProduct,
                        user: testUser,
                      ),
                    ),
                  );
                },
                child: const Text('View Product'),
              ),
            ),
          ),
        ),
      );

      // Navigate to product detail
      await tester.tap(find.text('View Product'));
      await tester.pumpAndSettle();

      // Verify back button exists
      expect(find.byType(BackButton), findsOneWidget);

      // Navigate back
      await tester.tap(find.byType(BackButton));
      await tester.pumpAndSettle();

      // Should be back
      expect(find.text('View Product'), findsOneWidget);
    });

    testWidgets('complete navigation flow: products -> profile -> back',
        (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: ProductsScreen(user: testUser),
        ),
      );

      // Start at products screen
      await tester.pump();
      expect(find.text('Products'), findsOneWidget);

      // Go to profile
      await tester.tap(find.byIcon(Icons.person));
      await tester.pumpAndSettle();
      expect(find.text('Profile'), findsOneWidget);
      expect(find.text('Name: Test User'), findsOneWidget);

      // Go back to products
      await tester.tap(find.byType(BackButton));
      await tester.pumpAndSettle();
      expect(find.text('Products'), findsOneWidget);
    });

    testWidgets('complete navigation flow: products -> orders -> back',
        (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: ProductsScreen(user: testUser),
        ),
      );

      // Start at products screen
      await tester.pump();
      expect(find.text('Products'), findsOneWidget);

      // Go to orders
      await tester.tap(find.byIcon(Icons.shopping_cart));
      await tester.pumpAndSettle();
      expect(find.text('My Orders'), findsOneWidget);

      // Go back to products
      await tester.tap(find.byType(BackButton));
      await tester.pumpAndSettle();
      expect(find.text('Products'), findsOneWidget);
    });
  });
}
