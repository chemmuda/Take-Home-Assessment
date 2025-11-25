import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:qa_assessment_app/models/user.dart';
import 'package:qa_assessment_app/screens/orders_screen.dart';

void main() {
  group('OrdersScreen Widget', () {
    final testUser = User(
      id: 1,
      name: 'Test User',
      email: 'test@example.com',
    );

    testWidgets('should display My Orders in app bar', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: OrdersScreen(user: testUser),
        ),
      );

      expect(
        find.descendant(
          of: find.byType(AppBar),
          matching: find.text('My Orders'),
        ),
        findsOneWidget,
      );
    });

    testWidgets('should show loading indicator initially', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: OrdersScreen(user: testUser),
        ),
      );

      expect(find.byType(CircularProgressIndicator), findsOneWidget);
    });

    testWidgets('should have back button in app bar', (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: Builder(
              builder: (context) => ElevatedButton(
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => OrdersScreen(user: testUser),
                    ),
                  );
                },
                child: const Text('Go to Orders'),
              ),
            ),
          ),
        ),
      );

      await tester.tap(find.text('Go to Orders'));
      await tester.pumpAndSettle();

      expect(find.byType(BackButton), findsOneWidget);
    });
  });
}
