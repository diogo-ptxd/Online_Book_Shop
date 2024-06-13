<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/images/bookwise_logo.png">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Register Book - BookWise</title>
</head>

<body>
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.html?rand=<?php echo rand(); ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Register Book</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-3">
        <h1 class="text-center">Register Your Book</h1>
        <form action="../scripts/register_book.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="genre">Genre</label>
                <select class="form-control" id="genre" name="genre" required>
                    <option value="">Select Genre</option>
                    <option value="Fantasy">Fantasy</option>
                    <option value="Science Fiction">Science Fiction</option>
                    <option value="Mystery">Mystery</option>
                    <option value="Romance">Romance</option>
                    <!-- Add more genres as needed -->
                </select>
            </div>
            <div class="form-group">
                <label for="title">Book Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="description">Description (Max 255 letters)</label>
                <textarea class="form-control" id="description" name="description" rows="4" required
                    maxlength="255"></textarea>
                <small class="form-text text-muted">Remaining letters: <span
                        id="letterCount">255</span></small>
            </div>
            <div class="form-group">
                <label for="cover_image">Cover Image</label>
                <input type="file" class="form-control-file" id="cover_image" name="cover_image" accept="image/*" required>
            </div>
            <!-- Cover image preview -->
            <div class="form-group">
                <label>Cover Image Preview</label>
                <img id="coverPreview" class="img-thumbnail" src="#" alt="Cover Preview" style="display: none;">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register Book</button>
        </form>
    </div>

    <script>
        // Function to update the letter count as the user types
        document.getElementById('description').addEventListener('input', function () {
            var maxLength = 255;
            var currentLength = this.value.length;
            var remaining = maxLength - currentLength;

            if (remaining < 0) {
                // Truncate description to 255 letters
                this.value = this.value.slice(0, maxLength);
                remaining = 0;
            }

            document.getElementById('letterCount').textContent = remaining;
        });
    </script>



    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Cover image preview
        $("#cover_image").change(function () {
            readURL(this);
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#coverPreview').attr('src', e.target.result);
                    $('#coverPreview').css('display', 'block');
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>
