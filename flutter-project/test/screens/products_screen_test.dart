import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:qa_assessment_app/models/user.dart';
import 'package:qa_assessment_app/screens/products_screen.dart';

void main() {
  group('ProductsScreen Widget', () {
    final testUser = User(
      id: 1,
      name: 'Test User',
      email: 'test@example.com',
    );

    testWidgets('should display Products in app bar', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: ProductsScreen(user: testUser),
        ),
      );

      expect(
        find.descendant(
          of: find.byType(AppBar),
          matching: find.text('Products'),
        ),
        findsOneWidget,
      );
    });

    testWidgets('should display shopping cart icon in app bar',
        (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: ProductsScreen(user: testUser),
        ),
      );

      expect(
        find.descendant(
          of: find.byType(AppBar),
          matching: find.byIcon(Icons.shopping_cart),
        ),
        findsOneWidget,
      );
    });

    testWidgets('should display person icon in app bar', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: ProductsScreen(user: testUser),
        ),
      );

      expect(
        find.descendant(
          of: find.byType(AppBar),
          matching: find.byIcon(Icons.person),
        ),
        findsOneWidget,
      );
    });

    testWidgets('should show loading indicator initially', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: ProductsScreen(user: testUser),
        ),
      );

      expect(find.byType(CircularProgressIndicator), findsOneWidget);
    });

    testWidgets('should have two IconButtons in app bar', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: ProductsScreen(user: testUser),
        ),
      );

      expect(
        find.descendant(
          of: find.byType(AppBar),
          matching: find.byType(IconButton),
        ),
        findsNWidgets(2),
      );
    });
  });
}
