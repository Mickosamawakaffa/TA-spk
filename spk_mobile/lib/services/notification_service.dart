import 'dart:io';

import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import '../models/user.dart';
import 'auth_service.dart';

class NotificationService {
  static final NotificationService _instance = NotificationService._internal();
  factory NotificationService() => _instance;
  NotificationService._internal();

  final FirebaseMessaging _messaging = FirebaseMessaging.instance;
  final FlutterLocalNotificationsPlugin _localNotifications =
      FlutterLocalNotificationsPlugin();

  static const AndroidNotificationChannel _channel = AndroidNotificationChannel(
    'admin_reminders',
    'Admin Reminders',
    description: 'Notifikasi pengingat admin mingguan',
    importance: Importance.high,
  );

  Future<void> init(AuthService authService) async {
    await _initLocalNotifications();
    await _requestPermissions();

    final user = authService.currentUser;
    if (_isAdminUser(user)) {
      await _registerToken(authService);
      _messaging.onTokenRefresh.listen((token) {
        authService.registerDeviceToken(
          token: token,
          platform: _platformName(),
        );
      });
    }

    FirebaseMessaging.onMessage.listen((message) async {
      await _showLocalNotification(message);
    });
  }

  bool _isAdminUser(User? user) {
    if (user == null) return false;
    return user.isAdmin() || user.isSuperAdmin();
  }

  Future<void> _registerToken(AuthService authService) async {
    final token = await _messaging.getToken();
    if (token == null) return;

    await authService.registerDeviceToken(
      token: token,
      platform: _platformName(),
    );
  }

  Future<void> _initLocalNotifications() async {
    const androidSettings = AndroidInitializationSettings(
      '@mipmap/ic_launcher',
    );
    const iosSettings = DarwinInitializationSettings();
    const settings = InitializationSettings(
      android: androidSettings,
      iOS: iosSettings,
    );

    await _localNotifications.initialize(settings);

    await _localNotifications
        .resolvePlatformSpecificImplementation<
          AndroidFlutterLocalNotificationsPlugin
        >()
        ?.createNotificationChannel(_channel);
  }

  Future<void> _requestPermissions() async {
    await _messaging.requestPermission(alert: true, badge: true, sound: true);
  }

  Future<void> _showLocalNotification(RemoteMessage message) async {
    final notification = message.notification;
    if (notification == null) return;

    final androidDetails = AndroidNotificationDetails(
      _channel.id,
      _channel.name,
      channelDescription: _channel.description,
      importance: Importance.high,
      priority: Priority.high,
    );

    final details = NotificationDetails(android: androidDetails);

    await _localNotifications.show(
      notification.hashCode,
      notification.title,
      notification.body,
      details,
    );
  }

  String _platformName() {
    if (Platform.isAndroid) return 'android';
    if (Platform.isIOS) return 'ios';
    return 'unknown';
  }
}
