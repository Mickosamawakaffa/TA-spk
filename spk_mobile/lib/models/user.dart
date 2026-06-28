class User {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String role;
  final String? roleLabel;
  final String? userType;
  final DateTime? createdAt;
  final DateTime? emailVerifiedAt;

  User({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    required this.role,
    this.roleLabel,
    this.userType,
    this.createdAt,
    this.emailVerifiedAt,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] ?? 0,
      name: (json['name'] ?? json['nama'] ?? json['username'] ?? '').toString(),
      email: (json['email'] ?? json['email_address'] ?? '').toString(),
      phone: (json['phone'] ?? json['no_hp'] ?? json['no_telepon'])?.toString(),
      role: json['role'] ?? 'user',
      roleLabel: json['role_label'],
      userType: json['user_type'],
      createdAt: json['created_at'] != null
          ? DateTime.parse(json['created_at'])
          : null,
      emailVerifiedAt: json['email_verified_at'] != null
          ? DateTime.parse(json['email_verified_at'])
          : null,
    );
  }

  /// Get user-friendly role label
  String getRoleLabel() {
    if (roleLabel != null && roleLabel!.isNotEmpty) {
      return roleLabel!;
    }

    switch (role) {
      case 'super_admin':
        return 'Super Admin';
      case 'admin':
        return 'Admin';
      case 'user':
        return 'Mahasiswa';
      default:
        return role;
    }
  }

  /// Check if user is mahasiswa
  bool isMahasiswa() => role == 'user';

  /// Check if user is admin
  bool isAdmin() => role == 'admin';

  /// Check if user is super admin
  bool isSuperAdmin() => role == 'super_admin';

  /// Check if email is verified
  bool get isEmailVerified => emailVerifiedAt != null;

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'role': role,
      'role_label': roleLabel,
      'user_type': userType,
      'created_at': createdAt?.toIso8601String(),
      'email_verified_at': emailVerifiedAt?.toIso8601String(),
    };
  }
}
