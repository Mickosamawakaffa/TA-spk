import 'dart:convert';
import 'dart:typed_data';
import 'package:flutter/widgets.dart';
import 'package:flutter/services.dart';
import 'local_placeholder.dart';

/// AppPlaceholder attempts to load `assets/placeholder.b64` (base64 PNG)
/// and render it with Image.memory. If the asset is missing or fails,
/// it falls back to `LocalPlaceholder` (embedded tiny PNG).
class AppPlaceholder extends StatefulWidget {
  final double? height;
  final double? width;
  final BoxFit fit;

  const AppPlaceholder({
    Key? key,
    this.height,
    this.width,
    this.fit = BoxFit.cover,
  }) : super(key: key);

  @override
  State<AppPlaceholder> createState() => _AppPlaceholderState();
}

class _AppPlaceholderState extends State<AppPlaceholder> {
  static Uint8List? _cachedBytes;
  late Future<Uint8List?> _loadFuture;

  @override
  void initState() {
    super.initState();
    _loadFuture = _ensureLoaded();
  }

  Future<Uint8List?> _ensureLoaded() async {
    if (_cachedBytes != null) return _cachedBytes;
    // Load placeholder.png as binary; if not present, fallback to embedded LocalPlaceholder
    try {
      final bd = await rootBundle.load('assets/placeholder.png');
      final bytes = bd.buffer.asUint8List();
      if (bytes.isNotEmpty) {
        _cachedBytes = bytes;
        return _cachedBytes;
      }
    } catch (_) {
      // ignore and fallback to embedded
    }

    return null;
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<Uint8List?>(
      future: _loadFuture,
      builder: (context, snap) {
        if (snap.connectionState == ConnectionState.done && snap.data != null) {
          return Image.memory(
            snap.data!,
            height: widget.height,
            width: widget.width,
            fit: widget.fit,
          );
        }
        // Fallback to embedded LocalPlaceholder while loading or on error
        return LocalPlaceholder(
          height: widget.height,
          width: widget.width,
          fit: widget.fit,
        );
      },
    );
  }
}
