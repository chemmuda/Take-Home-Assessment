import 'package:flutter_test/flutter_test.dart';
import 'package:qa_assessment_app/models/user.dart';

void main() {
  group('User Model', () {
    test('should create User from JSON', () {
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

    test('should create User from JSON without role', () {
      final json = {
        'id': 2,
        'name': 'Jane Smith',
        'email': 'jane@example.com',
      };

      final user = User.fromJson(json);

      expect(user.id, 2);
      expect(user.name, 'Jane Smith');
      expect(user.email, 'jane@example.com');
      expect(user.role, isNull);
    });

    test('should convert User to JSON', () {
      final user = User(
        id: 3,
        name: 'Bob Johnson',
        email: 'bob@example.com',
        role: 'user',
      );

      final json = user.toJson();

      expect(json['id'], 3);
      expect(json['name'], 'Bob Johnson');
      expect(json['email'], 'bob@example.com');
      expect(json['role'], 'user');
    });

    test('should convert User to JSON with null role', () {
      final user = User(
        id: 4,
        name: 'Alice Brown',
        email: 'alice@example.com',
      );

      final json = user.toJson();

      expect(json['id'], 4);
      expect(json['name'], 'Alice Brown');
      expect(json['email'], 'alice@example.com');
      expect(json['role'], isNull);
    });
  });
}
