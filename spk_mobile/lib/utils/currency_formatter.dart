/// Utility class for formatting Indonesian Rupiah currency
class CurrencyFormatter {
  /// Format a number to full Rupiah format: "Rp 8.500.000"
  static String formatRupiah(double amount) {
    final formatted = amount
        .toStringAsFixed(0)
        .replaceAllMapped(
          RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
          (Match m) => '${m[1]}.',
        );
    return 'Rp $formatted';
  }

  /// Format to compact readable format:
  /// - < 1000: "Rp 500"
  /// - 1000-999999: "Rp 5rb" or "Rp 50rb"
  /// - 1000000+: "Rp 1jt" or "Rp 8,5jt"
  static String formatCompact(double amount) {
    if (amount >= 1000000) {
      final juta = amount / 1000000;
      if (juta == juta.roundToDouble()) {
        return 'Rp ${juta.toStringAsFixed(0)}jt';
      }
      return 'Rp ${juta.toStringAsFixed(1).replaceAll('.0', '')}jt';
    } else if (amount >= 1000) {
      final ribu = amount / 1000;
      if (ribu == ribu.roundToDouble()) {
        return 'Rp ${ribu.toStringAsFixed(0)}rb';
      }
      return 'Rp ${ribu.toStringAsFixed(1).replaceAll('.0', '')}rb';
    }
    return 'Rp ${amount.toStringAsFixed(0)}';
  }

  /// Format for price badge on cards - clear and unambiguous
  /// e.g. "Rp 8.500.000" for millions, "Rp 5.000" for thousands
  static String formatCardPrice(double amount) {
    return formatRupiah(amount);
  }

  /// Format for filter slider labels - compact
  static String formatSlider(double amount) {
    return formatCompact(amount);
  }
}
