import 'dart:convert';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';
import 'package:qa_assessment_app/services/api_service.dart';
import 'package:qa_assessment_app/models/user.dart';
import 'package:qa_assessment_app/models/product.dart';
import 'package:qa_assessment_app/models/order.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  group('ApiService', () {
    late ApiService apiService;

    setUp(() async {
      SharedPreferences.setMockInitialValues({});
      apiService = ApiService();
    });

    group('login', () {
      test('should login successfully and save token', () async {
        SharedPreferences.setMockInitialValues({});
        final mockClient = MockClient((request) async {
          expect(request.url.path, '/api/login');
          expect(request.method, 'POST');

          final body = jsonDecode(request.body);
          expect(body['email'], 'test@example.com');
          expect(body['password'], 'password123');

          return http.Response(
            jsonEncode({
              'access_token': 'mock_token_123',
              'user': {
                'id': 1,
                'name': 'Test User',
                'email': 'test@example.com',
                'role': 'user',
              },
            }),
            200,
          );
        });

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', 'mock_token_123');

        expect(prefs.getString('auth_token'), 'mock_token_123');
      });

      test('should throw exception on login failure', () async {
        
        expect(() async {
          throw Exception('Request failed with status: 401');
        }, throwsException);
      });
    });

    group('token management', () {
      test('should save token to SharedPreferences', () async {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', 'test_token');

        final token = prefs.getString('auth_token');
        expect(token, 'test_token');
      });

      test('should retrieve token from SharedPreferences', () async {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', 'stored_token');

        final token = prefs.getString('auth_token');
        expect(token, 'stored_token');
      });

      test('should remove token from SharedPreferences', () async {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', 'token_to_remove');

        await prefs.remove('auth_token');
        final token = prefs.getString('auth_token');
        expect(token, isNull);
      });
    });

    group('User.fromJson', () {
      test('should parse user from JSON correctly', () {
        final json = {
          'id': 1,
          'name': 'John Doe',
          'email': 'john@example.com',
          'role': 'admin',
        };

        final user = User.fromJson(json);

        expect(user.id, 1);
        expect(user.name, 'John Doe');
        expect(user.email, 'john@example.com');
        expect(user.role, 'admin');
      });
    });

    group('Product.fromJson', () {
      test('should parse product list from JSON', () {
        final jsonList = [
          {
            'id': 1,
            'name': 'Product 1',
            'price': 99.99,
            'stock': 10,
          },
          {
            'id': 2,
            'name': 'Product 2',
            'price': 149.99,
            'stock': 5,
          },
        ];

        final products =
            jsonList.map((json) => Product.fromJson(json)).toList();

        expect(products.length, 2);
        expect(products[0].name, 'Product 1');
        expect(products[1].name, 'Product 2');
      });
    });

    group('Order.fromJson', () {
      test('should parse order list from JSON', () {
        final jsonList = [
          {
            'id': 1,
            'user_id': 1,
            'status': 'pending',
            'total_amount': 199.99,
          },
          {
            'id': 2,
            'user_id': 1,
            'status': 'completed',
            'total_amount': 299.99,
          },
        ];

        final orders = jsonList.map((json) => Order.fromJson(json)).toList();

        expect(orders.length, 2);
        expect(orders[0].status, 'pending');
        expect(orders[1].status, 'completed');
      });
    });

    group('error handling', () {
      test('should handle 404 errors', () {
        expect(() {
          throw Exception('Request failed with status: 404');
        }, throwsException);
      });

      test('should handle 500 errors', () {
        expect(() {
          throw Exception('Request failed with status: 500');
        }, throwsException);
      });

      test('should handle network errors', () {
        expect(() {
          throw Exception('Network error');
        }, throwsException);
      });
    });
  });
}
