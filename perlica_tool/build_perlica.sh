#!/bin/sh
# Build all airport/app/mode combinations into perlica_build beside this script.
set -eu

case "${1:-}" in
  -h|--help)
    echo "Usage: sh build_perlica.sh"
    echo "Build all eight APKs into perlica_build/ with SHA-1 checksum files."
    echo "Requires flutter and sha1sum on PATH. Existing output files are replaced."
    exit 0
    ;;
esac
if [ "$#" -ne 0 ]; then
  echo "Usage: sh build_perlica.sh" >&2
  exit 1
fi

for tool in flutter sha1sum; do
  if ! command -v "$tool" >/dev/null 2>&1; then
    echo "Error: $tool is required on PATH." >&2
    exit 1
  fi
done

root=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
output="$root/perlica_build"

for airport in husein supadio; do
  for app in driver user; do
    case "$app" in
      driver) project=driver; basename=gofly-driver ;;
      user) project=passenger; basename=gofly ;;
    esac

    for mode in debug release; do
      filename="$basename.apk"
      if [ "$mode" = debug ]; then
        filename="$basename-debug.apk"
      fi
      destination="$output/$airport/$app/$mode"
      source="$root/$project/build/app/outputs/flutter-apk/app-$mode.apk"

      printf '\nBuilding %s / %s / %s...\n' "$airport" "$app" "$mode"
      sh "$root/$project/perlica.sh" build "$airport" "--$mode"
      if [ ! -f "$source" ]; then
        echo "Error: expected APK not found: $source" >&2
        exit 1
      fi

      mkdir -p "$destination"
      cp "$source" "$destination/$filename"
      # Relative filenames allow verification after moving the output directory.
      (
        cd "$destination"
        sha1sum "$filename" > "$filename.sha1"
      )
      printf 'Saved %s/%s\n' "$destination" "$filename"
    done
  done
done

printf '\nAll APKs and SHA-1 files are ready in %s\n' "$output"
