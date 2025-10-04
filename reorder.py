import os
from datetime import datetime

# Folder containing your images
FOLDER = "."
# Reverse order? (True = newest first, False = oldest first)
DESCENDING = False  # set to False so oldest = 001

# Get all image files
files = [f for f in os.listdir(FOLDER) if f.lower().endswith(('.jpg', '.jpeg', '.png', '.gif', '.webp'))]

# Sort by modification time
files.sort(key=lambda f: os.path.getmtime(os.path.join(FOLDER, f)), reverse=DESCENDING)

# Rename with numbering
for i, filename in enumerate(files, 1):
    ext = os.path.splitext(filename)[1].lower()
    new_name = f"{i:03d}{ext}"  # 001.jpg, 002.jpg, etc.
    old_path = os.path.join(FOLDER, filename)
    new_path = os.path.join(FOLDER, new_name)

    # Avoid overwriting existing files
    if os.path.exists(new_path):
        new_path = os.path.join(FOLDER, f"{i:03d}_new{ext}")

    os.rename(old_path, new_path)
    print(f"Renamed {filename} -> {os.path.basename(new_path)}")

print("✅ Done renaming (oldest first)!")
