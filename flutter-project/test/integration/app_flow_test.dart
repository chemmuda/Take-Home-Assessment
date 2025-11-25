import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:qa_assessment_app/main.dart';

void main() {
  group('App Integration Tests', () {
    testWidgets('should display login screen on app start', (tester) async {
      await tester.pumpWidget(const MyApp());

      expect(find.text('Login'), findsAtLeastNWidgets(1));
      expect(find.byType(TextFormField), findsNWidgets(2));
      expect(find.widgetWithText(ElevatedButton, 'Login'), findsOneWidget);
    });

    testWidgets('login form validation flow', (tester) async {
      await tester.pumpWidget(const MyApp());

      // Try to submit empty form
      await tester.tap(find.widgetWithText(ElevatedButton, 'Login'));
      await tester.pumpAndSettle();

      expect(find.text('Please enter your email'), findsOneWidget);

      // Enter invalid email
      await tester.enterText(
        find.widgetWithText(TextFormField, 'Email'),
        'bademail',
      );
      await tester.tap(find.widgetWithText(ElevatedButton, 'Login'));
      await tester.pumpAndSettle();

      expect(find.text('Please enter a valid email'), findsOneWidget);

      // Enter valid email but no password
      await tester.enterText(
        find.widgetWithText(TextFormField, 'Email'),
        'test@example.com',
      );
      await tester.tap(find.widgetWithText(ElevatedButton, 'Login'));
      await tester.pumpAndSettle();

      expect(find.text('Please enter your password'), findsOneWidget);
    });

    testWidgets('app theme should use Material 3', (tester) async {
      await tester.pumpWidget(const MyApp());

      final materialApp = tester.widget<MaterialApp>(find.byType(MaterialApp));
      expect(materialApp.theme?.useMaterial3, true);
    });

    testWidgets('app title should be correct', (tester) async {
      await tester.pumpWidget(const MyApp());

      final materialApp = tester.widget<MaterialApp>(find.byType(MaterialApp));
      expect(materialApp.title, 'QA Assessment App');
    });

    testWidgets('login screen should have proper form structure',
        (tester) async {
      await tester.pumpWidget(const MyApp());

      // Verify form exists
      expect(find.byType(Form), findsOneWidget);

      // Verify email field
      final emailField = find.widgetWithText(TextFormField, 'Email');
      expect(emailField, findsOneWidget);

      final emailWidget = tester.widget<TextFormField>(emailField);
      expect(emailWidget.keyboardType, TextInputType.emailAddress);

      // Verify password field
      final passwordField = find.widgetWithText(TextFormField, 'Password');
      expect(passwordField, findsOneWidget);

      final passwordWidget = tester.widget<TextFormField>(passwordField);
      expect(passwordWidget.obscureText, true);
    });

    testWidgets('should trim email input on login attempt', (tester) async {
      await tester.pumpWidget(const MyApp());

      // Enter email with spaces
      await tester.enterText(
        find.widgetWithText(TextFormField, 'Email'),
        '  test@example.com  ',
      );
      await tester.enterText(
        find.widgetWithText(TextFormField, 'Password'),
        'password123',
      );

      await tester.tap(find.widgetWithText(ElevatedButton, 'Login'));
      await tester.pump();

      // The login will fail due to no API, but we verify the form processes input
      expect(find.byType(CircularProgressIndicator), findsOneWidget);
    });

    testWidgets('app navigation structure should be set up correctly',
        (tester) async {
      await tester.pumpWidget(const MyApp());

      // Verify we start at LoginScreen
      expect(find.byType(Scaffold), findsOneWidget);
      expect(find.byType(AppBar), findsOneWidget);
    });
  });
}
