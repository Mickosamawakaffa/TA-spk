import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_spinkit/flutter_spinkit.dart';
import '../services/auth_service.dart';
import 'improved_home_screen.dart';
import '../login.dart';

class EmailVerificationScreen extends StatefulWidget {
  const EmailVerificationScreen({super.key});

  @override
  State<EmailVerificationScreen> createState() => _EmailVerificationScreenState();
}

class _EmailVerificationScreenState extends State<EmailVerificationScreen> {
  final _authService = AuthService();
  bool _isLoading = false;
  bool _isResending = false;
  int _cooldownSeconds = 0;
  Timer? _cooldownTimer;
  String? _message;
  bool _isSuccessMessage = true;

  @override
  void initState() {
    super.initState();
    // Auto-check once on load
    _checkVerificationStatus(silent: true);
  }

  @override
  void dispose() {
    _cooldownTimer?.cancel();
    super.dispose();
  }

  void _startCooldown() {
    setState(() => _cooldownSeconds = 60);
    _cooldownTimer?.cancel();
    _cooldownTimer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_cooldownSeconds > 0) {
        setState(() => _cooldownSeconds--);
      } else {
        _cooldownTimer?.cancel();
      }
    });
  }

  Future<void> _checkVerificationStatus({bool silent = false}) async {
    if (!mounted) return;
    if (!silent) {
      setState(() {
        _isLoading = true;
        _message = null;
      });
    }

    // Refresh user data from API
    final user = await _authService.getCurrentUser();

    if (!mounted) return;
    setState(() => _isLoading = false);

    if (user != null && user.isEmailVerified) {
      // Success! Go to Home Screen
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Row(
              children: [
                const Icon(Icons.check_circle, color: Colors.white),
                const SizedBox(width: 8),
                Text('Selamat, email Anda telah terverifikasi!'),
              ],
            ),
            backgroundColor: Colors.green[700],
          ),
        );
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => const ImprovedHomeScreen()),
        );
      }
    } else {
      if (!silent) {
        setState(() {
          _message = 'Email belum diverifikasi. Silakan klik tautan di inbox Anda lalu coba lagi.';
          _isSuccessMessage = false;
        });
      }
    }
  }

  Future<void> _handleResendEmail() async {
    if (_cooldownSeconds > 0) return;

    setState(() {
      _isResending = true;
      _message = null;
    });

    final result = await _authService.resendVerificationEmail();

    if (!mounted) return;
    setState(() => _isResending = false);

    if (result['success'] == true) {
      _startCooldown();
      setState(() {
        _message = 'Link verifikasi baru telah dikirim ke email Anda.';
        _isSuccessMessage = true;
      });
    } else {
      setState(() {
        _message = result['message'] ?? 'Gagal mengirim email verifikasi.';
        _isSuccessMessage = false;
      });
    }
  }

  Future<void> _handleLogout() async {
    setState(() => _isLoading = true);
    await _authService.logout();
    if (!mounted) return;
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (_) => const LoginScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    final user = _authService.currentUser;
    final email = user?.email ?? '';

    return Scaffold(
      backgroundColor: const Color(0xFFF3F7FB),
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // Icon Animation / Header
                Container(
                  padding: const EdgeInsets.all(28),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.05),
                        blurRadius: 20,
                        offset: const Offset(0, 10),
                      ),
                    ],
                  ),
                  child: const SpinKitDoubleBounce(
                    color: Color(0xFF1565C0),
                    size: 60,
                  ),
                ),
                const SizedBox(height: 32),

                // Card Container
                Container(
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(24),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.03),
                        blurRadius: 15,
                        offset: const Offset(0, 5),
                      ),
                    ],
                  ),
                  child: Column(
                    children: [
                      const Text(
                        'Verifikasi Email Anda',
                        style: TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.w800,
                          color: Color(0xFF1A1A2E),
                          letterSpacing: -0.5,
                        ),
                      ),
                      const SizedBox(height: 12),
                      RichText(
                        textAlign: TextAlign.center,
                        text: TextSpan(
                          style: const TextStyle(
                            fontSize: 14,
                            color: Color(0xFF5A6B85),
                            height: 1.5,
                          ),
                          children: [
                            const TextSpan(text: 'Kami telah mengirimkan link verifikasi ke email: \n'),
                            TextSpan(
                              text: email,
                              style: const TextStyle(
                                fontWeight: FontWeight.bold,
                                color: Color(0xFF1565C0),
                              ),
                            ),
                            const TextSpan(
                              text: '\n\nSilakan buka kotak masuk (atau folder spam) Anda, klik link tersebut, kemudian kembali ke aplikasi ini.',
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Status Message Box if any
                      if (_message != null) ...[
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: _isSuccessMessage ? Colors.green[50] : Colors.orange[50],
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: _isSuccessMessage
                                  ? Colors.green[200]!
                                  : Colors.orange[200]!,
                            ),
                          ),
                          child: Row(
                            children: [
                              Icon(
                                _isSuccessMessage
                                    ? Icons.check_circle_outline
                                    : Icons.info_outline,
                                color: _isSuccessMessage ? Colors.green[700] : Colors.orange[700],
                                size: 20,
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Text(
                                  _message!,
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: _isSuccessMessage
                                        ? Colors.green[800]
                                        : Colors.orange[800],
                                    fontWeight: FontWeight.w500,
                                    height: 1.4,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 24),
                      ],

                      // Button Saya Sudah Verifikasi
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: _isLoading ? null : () => _checkVerificationStatus(),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF1565C0),
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14),
                            ),
                          ),
                          child: _isLoading
                              ? const SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2.5,
                                    valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                  ),
                                )
                              : const Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Icon(Icons.verified, size: 18, color: Colors.white),
                                    SizedBox(width: 8),
                                    Text(
                                      'SAYA SUDAH VERIFIKASI',
                                      style: TextStyle(
                                        fontSize: 14,
                                        fontWeight: FontWeight.bold,
                                        color: Colors.white,
                                      ),
                                    ),
                                  ],
                                ),
                        ),
                      ),
                      const SizedBox(height: 12),

                      // Button Resend Email
                      SizedBox(
                        width: double.infinity,
                        child: OutlinedButton(
                          onPressed: (_isResending || _cooldownSeconds > 0)
                              ? null
                              : _handleResendEmail,
                          style: OutlinedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14),
                            ),
                            side: const BorderSide(color: Color(0xFFE2EAF3)),
                          ),
                          child: _isResending
                              ? const SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(strokeWidth: 2),
                                )
                              : Text(
                                  _cooldownSeconds > 0
                                      ? 'KIRIM ULANG EMAIL ($_cooldownSeconds s)'
                                      : 'KIRIM ULANG EMAIL VERIFIKASI',
                                  style: TextStyle(
                                    fontSize: 13,
                                    fontWeight: FontWeight.bold,
                                    color: _cooldownSeconds > 0 ? Colors.grey : const Color(0xFF1565C0),
                                  ),
                                ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 24),

                // Button Logout/Back to Login
                TextButton(
                  onPressed: _isLoading ? null : _handleLogout,
                  child: const Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.logout, size: 16),
                      SizedBox(width: 6),
                      Text('Keluar / Ganti Akun'),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
