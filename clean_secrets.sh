#!/bin/bash

echo "🔍 Deep scanning entire project (including subfolders) using TruffleHog..."

# Run trufflehog deep scan on current directory
results=$(trufflehog filesystem --directory . --json 2>/dev/null)

# Extract file paths using safe grep and cut
files=$(echo "$results" | grep '"E:\DSS\Printify\Printify":' | cut -d'"' -f4 | sort | uniq)

if [ -z "$files" ]; then
  echo "✅ No secrets found. Safe to push!"
else
  echo "⚠️ Secrets detected in the following files:"
  echo "$files"

  # Delete all files that contain secrets
  for file in $files; do
    if [ -f "$file" ]; then
      echo "🗑 Deleting $file..."
      git rm --cached "$file" 2>/dev/null
      rm -f "$file"
    fi
  done

  # Commit the deletion
  git commit -m "🔒 Removed secret-containing files (auto-clean)"
  echo "✅ Files removed. Ready to push safely!"
fi
