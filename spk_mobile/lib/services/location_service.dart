import 'package:geolocator/geolocator.dart';
import 'dart:math';

class LocationService {
  static final LocationService _instance = LocationService._internal();
  
  factory LocationService() => _instance;
  LocationService._internal();

  // Request location permission
  Future<bool> requestLocationPermission() async {
    final permission = await Geolocator.checkPermission();
    
    if (permission == LocationPermission.denied) {
      final result = await Geolocator.requestPermission();
      return result == LocationPermission.whileInUse || 
             result == LocationPermission.always;
    } else if (permission == LocationPermission.deniedForever) {
      // Permission permanently denied, open app settings
      await Geolocator.openLocationSettings();
      return false;
    }
    
    return true;
  }

  // Get current location
  Future<Position?> getCurrentLocation() async {
    try {
      // Check permission first
      final hasPermission = await requestLocationPermission();
      if (!hasPermission) return null;

      final position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
        timeLimit: const Duration(seconds: 10),
      );
      
      return position;
    } catch (e) {
      print('Error getting location: $e');
      return null;
    }
  }

  // Calculate distance between two coordinates (in km)
  static double calculateDistance(
    double lat1, 
    double lon1, 
    double lat2, 
    double lon2,
  ) {
    const earthRadius = 6371; // km

    final dLat = _degreesToRadians(lat2 - lat1);
    final dLon = _degreesToRadians(lon2 - lon1);

    final a = sin(dLat / 2) * sin(dLat / 2) +
        cos(_degreesToRadians(lat1)) *
            cos(_degreesToRadians(lat2)) *
            sin(dLon / 2) *
            sin(dLon / 2);

    final c = 2 * atan2(sqrt(a), sqrt(1 - a));
    final distance = earthRadius * c;

    return distance;
  }

  static double _degreesToRadians(double degrees) {
    return degrees * pi / 180;
  }

  // Check if location services are enabled
  Future<bool> isLocationServiceEnabled() async {
    return await Geolocator.isLocationServiceEnabled();
  }
}
