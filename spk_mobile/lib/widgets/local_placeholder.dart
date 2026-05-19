import 'dart:convert';
import 'dart:typed_data';
import 'package:flutter/widgets.dart';

/// Tiny local placeholder image embedded as base64 to avoid external requests.
/// Returns an Image widget suitable for use in errorWidget / no-photo UI.
class LocalPlaceholder extends StatelessWidget {
  final double? height;
  final double? width;
  final BoxFit fit;

  const LocalPlaceholder({
    Key? key,
    this.height,
    this.width,
    this.fit = BoxFit.cover,
  }) : super(key: key);

  // 1x1 transparent PNG (very small). Using Image.memory so no asset file required.
  static const String _base64Png =
      'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVQYV2NgYAAAAAMAAWgmWQ0AAAAASUVORK5CYII=';

  @override
  Widget build(BuildContext context) {
    final bytes = base64Decode(_base64Png);
    return Image.memory(
      Uint8List.fromList(bytes),
      height: height,
      width: width,
      fit: fit,
    );
  }
}
