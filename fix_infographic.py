from PIL import Image
import numpy as np

img = Image.open("img/infographic-navigasi-strategis.png")
arr = np.array(img).copy()

# The infographic has:
# - Rows 0-182: thick green top border (no content)
# - Rows 183-209: title "Navigasi Strategis..." white text on green
# - Rows 210-225: transition area
# - Rows 226-477: white poster with pyramid diagram & icons
# - Rows 478-639: thick green bottom border (no content)
# - Cols 0-32: green left border
# - Cols 607-639: green right border

# Crop to content area including title
# Keep from row 178 (just before title) to row 478 (end of content)
# Keep from col 30 to col 610
top, bottom = 178, 479
left, right = 30, 610

# Replace green background with white in the title area (rows 178-226)
r, g, b = arr[:,:,0].astype(float), arr[:,:,1].astype(float), arr[:,:,2].astype(float)
green_mask = (g > r + 20) & (g > 100) & (r < 140) & (b > 80)

# In the title area, replace green with white  
# This will make white text disappear, so instead let's invert:
# Replace green with a dark color and keep text as-is
# Actually - title is white text on green. Better to make green -> dark navy to keep readable

# For the title area (rows 178 to 226), change green bg to dark color
title_area = arr[top:226, left:right, :].copy()
title_green = green_mask[top:226, left:right]

# Replace green pixels in title area with dark navy (#1a1a2e)
arr[top:226, left:right, 0][title_green] = 26   # R
arr[top:226, left:right, 1][title_green] = 26   # G  
arr[top:226, left:right, 2][title_green] = 46   # B

# For the content area side borders (cols 30-33 and 607-610), replace green with white
content_green = green_mask[226:bottom, left:right]
arr[226:bottom, left:right, 0][content_green] = 255
arr[226:bottom, left:right, 1][content_green] = 255
arr[226:bottom, left:right, 2][content_green] = 255

# Crop
cropped = arr[top:bottom, left:right, :]

# Save backup of original
import shutil
shutil.copy("img/infographic-navigasi-strategis.png", "img/infographic-navigasi-strategis-original.png")

# Save as high quality JPEG
result = Image.fromarray(cropped)
result.save("img/infographic-navigasi-strategis.png", quality=95, optimize=True)
print(f"Saved: {cropped.shape[1]}x{cropped.shape[0]} (was 640x640)")
print("Original backed up to img/infographic-navigasi-strategis-original.png")



