import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:qa_assessment_app/models/user.dart';
import 'package:qa_assessment_app/screens/profile_screen.dart';

void main() {
  group('ProfileScreen Widget', () {
    testWidgets('should display Profile in app bar', (tester) async {
      final testUser = User(
        id: 1,
        name: 'Test User',
        email: 'test@example.com',
      );

      await tester.pumpWidget(
        MaterialApp(
          home: ProfileScreen(user: testUser),
        ),
      );

      expect(
        find.descendant(
          of: find.byType(AppBar),
          matching: find.text('Profile'),
        ),
        findsOneWidget,
      );
    });

    testWidgets('should display user name and email after loading',
        (tester) async {
      final testUser = User(
        id: 2,
        name: 'John Doe',
        email: 'john@example.com',
      );

      await tester.pumpWidget(
        MaterialApp(
          home: ProfileScreen(user: testUser),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.text('Name: John Doe'), findsOneWidget);
      expect(find.text('Email: john@example.com'), findsOneWidget);
    });

    testWidgets('should display user role if available', (tester) async {
      final testUser = User(
        id: 3,
        name: 'Admin User',
        email: 'admin@example.com',
        role: 'admin',
      );

      await tester.pumpWidget(
        MaterialApp(
          home: ProfileScreen(user: testUser),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.text('Role: admin'), findsOneWidget);
    });

    testWidgets('should not display role if null', (tester) async {
      final testUser = User(
        id: 4,
        name: 'Regular User',
        email: 'user@example.com',
      );

      await tester.pumpWidget(
        MaterialApp(
          home: ProfileScreen(user: testUser),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.textContaining('Role:'), findsNothing);
    });

    testWidgets('should display Logout button', (tester) async {
      final testUser = User(
        id: 5,
        name: 'Test User',
        email: 'test@example.com',
      );

      await tester.pumpWidget(
        MaterialApp(
          home: ProfileScreen(user: testUser),
        ),
      );

      await tester.pumpAndSettle();

      expect(find.widgetWithText(ElevatedButton, 'Logout'), findsOneWidget);
    });

    testWidgets('should have back button in app bar', (tester) async {
      final testUser = User(
        id: 6,
        name: 'Test User',
        email: 'test@example.com',
      );

      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: Builder(
              builder: (context) => ElevatedButton(
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => ProfileScreen(user: testUser),
                    ),
                  );
                },
                child: const Text('Go to Profile'),
              ),
            ),
          ),
        ),
      );

      await tester.tap(find.text('Go to Profile'));
      await tester.pumpAndSettle();

      expect(find.byType(BackButton), findsOneWidget);
    });
  });
}
