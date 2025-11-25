import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:qa_assessment_app/models/product.dart';
import 'package:qa_assessment_app/widgets/product_card.dart';

void main() {
  group('ProductCard Widget', () {
    testWidgets('should display product name and price', (tester) async {
      final product = Product(
        id: 1,
        name: 'Test Product',
        price: 99.99,
        stock: 10,
      );

      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: ProductCard(
              product: product,
              onTap: () {},
            ),
          ),
        ),
      );

      expect(find.text('Test Product'), findsOneWidget);
      expect(find.text('\$99.99'), findsOneWidget);
    });

    testWidgets('should display shopping cart icon when in stock',
        (tester) async {
      final product = Product(
        id: 2,
        name: 'In Stock Product',
        price: 49.99,
        stock: 5,
      );

      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: ProductCard(
              product: product,
              onTap: () {},
            ),
          ),
        ),
      );

      expect(find.byIcon(Icons.shopping_cart), findsOneWidget);
      expect(find.text('Out of Stock'), findsNothing);
    });

    testWidgets('should display "Out of Stock" text when stock is zero',
        (tester) async {
      final product = Product(
        id: 3,
        name: 'Out of Stock Product',
        price: 149.99,
        stock: 0,
      );

      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: ProductCard(
              product: product,
              onTap: () {},
            ),
          ),
        ),
      );

      expect(find.text('Out of Stock'), findsOneWidget);
      expect(find.byIcon(Icons.shopping_cart), findsNothing);
    });

    testWidgets('should call onTap callback when tapped', (tester) async {
      final product = Product(
        id: 4,
        name: 'Tappable Product',
        price: 29.99,
        stock: 15,
      );

      bool wasTapped = false;

      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: ProductCard(
              product: product,
              onTap: () {
                wasTapped = true;
              },
            ),
          ),
        ),
      );

      await tester.tap(find.byType(ListTile));
      expect(wasTapped, true);
    });

    testWidgets('should display price with 2 decimal places', (tester) async {
      final product = Product(
        id: 5,
        name: 'Precise Price Product',
        price: 99.5,
        stock: 3,
      );

      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: ProductCard(
              product: product,
              onTap: () {},
            ),
          ),
        ),
      );

      expect(find.text('\$99.50'), findsOneWidget);
    });

    testWidgets('should render as a Card widget', (tester) async {
      final product = Product(
        id: 6,
        name: 'Card Product',
        price: 199.99,
        stock: 7,
      );

      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: ProductCard(
              product: product,
              onTap: () {},
            ),
          ),
        ),
      );

      expect(find.byType(Card), findsOneWidget);
      expect(find.byType(ListTile), findsOneWidget);
    });
  });
}
