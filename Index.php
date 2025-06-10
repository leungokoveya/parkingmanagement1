<?php
session_start();
include('includes/header.php');
?>

<!-- Header Section -->
<head>
    <style>
        .suggestions {
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            background: #fff;
            position: absolute;
            width: 100%;
            z-index: 999;
        }
        .suggestion-item {
            padding: 8px;
            cursor: pointer;
        }
        .suggestion-item:hover {
            background-color: #f0f0f0;
        }
        .search-wrapper {
            position: relative;
            width: 300px;
            margin: 0 auto;
        }
    </style>
</head>

<?php if (isset($_SESSION["UserID"])): ?>
    <section style="background-color: #eef3fa; padding: 20px; text-align: center; border-bottom: 1px solid #ccc;">
        <h2 style="margin-bottom: 8px; color: #004080;">Welcome, <?php echo htmlspecialchars($_SESSION["Firstname"]); ?>!</h2>
        
        <?php
        // Check if the user has a role assigned, and display a default role if not
        if (isset($_SESSION["Role"])) {
            echo '<p style="margin-bottom: 15px;">You are logged in as: <strong>' . htmlspecialchars($_SESSION["Role"]) . '</strong></p>';
        } else {
            echo '<p style="margin-bottom: 15px;">You are logged in as: <strong>Standard User</strong></p>';
        }
        ?>
    </section>
<?php else: ?>
    <section style="background-color: #eef3fa; padding: 20px; text-align: center; border-bottom: 1px solid #ccc;">
        <h2 style="margin-bottom: 8px; color: #004080;">Welcome, Guest!</h2>
        <p style="margin-bottom: 15px;">Please <a href="Login.php" style="color: #004080; text-decoration: none; font-weight: bold;">log in</a> to access your account.</p>
    </section>
<?php endif; ?>

<!-- Page Introduction -->
<h1 class="text-2xl font-bold text-center mt-6">Welcome to the RoppaCorp Carpark Management System</h1>
<p class="text-center text-gray-600 mt-2 px-6">
    This system is designed to help RoppaCorp Industries manage car park bookings across all three of their sites.
    Users can search for and book available spaces, view car park layouts, and manage bookings based on their role.
</p>

<!-- The rest of your HTML code follows -->

<body>
<!-- Search Section -->
<!-- Search Section -->
<section class="text-center py-10 px-6 relative">
    <h2 class="text-2xl font-semibold mb-2">Find Your Parking Space</h2>
    <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
        Search for parking spaces by selecting a site first, then choosing a space type (e.g., Electric, Visitor, or Disabled).
        After selection, all bays in that space will be displayed. Unavailable ones will be marked with ❌.
    </p>

    <!-- Dynamic Search Dropdowns -->
    <div class="w-full max-w-md mx-auto text-left">
        <label class="block font-medium mb-1">Select Site:</label>
        <select id="siteSelect" class="w-full border px-3 py-2 rounded mb-4">
            <option value="">-- Choose a Site --</option>
            <option value="1">Site A</option>  <!-- Site A ID -->
            <option value="2">Site B</option>  <!-- Site B ID -->
            <option value="3">Site C</option>  <!-- Site C ID -->
        </select>

        <label class="block font-medium mb-1 hidden" id="spaceTypeLabel">Select Parking Type:</label>
        <select id="spaceTypeSelect" class="w-full border px-3 py-2 rounded mb-4 hidden">
            <option value="">-- Choose Parking Type --</option>
        </select>
    </div>

    <!-- Bay Grid -->
    <div id="bayGrid" class="grid grid-cols-4 gap-4 mt-6 max-w-lg mx-auto"></div>
</section>

<!-- Parking Sites Info -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-6 px-10 pb-10">
    <!-- Site A -->
    <div class="border rounded p-4 text-left">
        <div class="h-40 bg-gray-100 flex items-center justify-center mb-4 text-4xl">🅿️ A</div>
        <h3 class="font-bold">Site A</h3>
        <ul class="text-sm text-gray-700">
            <li>49 SPACES</li>
            <li>- 21 Electric charging bays</li>
            <li>- 16 Disabled parking bays</li>
            <li>- 12 Visitor bays</li>
        </ul>
    </div>
 
    <!-- Site B -->
    <div class="border rounded p-4 text-left">
        <div class="h-40 bg-gray-100 flex items-center justify-center mb-4 text-4xl">🅿️ B</div>
        <h3 class="font-bold">Site B</h3>
        <ul class="text-sm text-gray-700">
            <li>32 SPACES</li>
            <li>- 14 Electric charging bays</li>
            <li>- 10 Disabled parking bays</li>
            <li>- 8 Visitor bays</li>
        </ul>
    </div>
 
    <!-- Site C -->
    <div class="border rounded p-4 text-left">
        <div class="h-40 bg-gray-100 flex items-center justify-center mb-4 text-4xl">🅿️ C</div>
        <h3 class="font-bold">Site C</h3>
        <ul class="text-sm text-gray-700">
            <li>28 SPACES</li>
            <li>- 12 Electric charging bays</li>
            <li>- 8 Disabled parking bays</li>
            <li>- 8 Visitor bays</li>
        </ul>
    </div>
</section>



<script>
document.addEventListener("DOMContentLoaded", () => {
    const siteSelect = document.getElementById("siteSelect");
    const spaceTypeSelect = document.getElementById("spaceTypeSelect");
    const spaceTypeLabel = document.getElementById("spaceTypeLabel");
    const bayGrid = document.getElementById("bayGrid");

    siteSelect.addEventListener("change", () => {

        if (!userID) {
        alert("Please log in to view parking spaces.");
        siteSelect.value = ""; // Reset dropdown
        return;
    }


        const siteID = siteSelect.value;
        spaceTypeSelect.innerHTML = `<option value="">-- Choose Parking Type --</option>`;
        bayGrid.innerHTML = "";
        
        // Clear any previous error messages
        const errorMessage = document.getElementById("errorMessage");
        if (errorMessage) {
            errorMessage.remove();
        }

        if (!siteID) return;

        // Show loading message
        const loadingMessage = document.createElement('div');
        loadingMessage.textContent = 'Loading space types...';
        loadingMessage.id = 'loadingMessage';
        bayGrid.appendChild(loadingMessage);

        // Fetch available space types based on SiteID
        fetch(`getSpaces.php?siteID=${siteID}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error('Failed to fetch space types');
                }
                return res.json();
            })
            .then(data => {
                // Remove loading message
                document.getElementById('loadingMessage').remove();

                if (data.error) {
                    displayError(data.error);
                    return;
                }

                if (data.length > 0) {
                    data.forEach(space => {
                        const opt = document.createElement("option");
                        opt.value = space.SpaceID;
                        opt.textContent = space.SpaceType;
                        spaceTypeSelect.appendChild(opt);
                    });
                    spaceTypeSelect.classList.remove("hidden");
                    spaceTypeLabel.classList.remove("hidden");
                } else {
                    displayError('No parking spaces found for the selected site.');
                }
            })
            .catch(error => {
                console.error(error);
                // Remove loading message
                document.getElementById('loadingMessage').remove();
                displayError('An error occurred while fetching parking spaces. Please try again.');
            });
    });

    spaceTypeSelect.addEventListener("change", () => {
    const spaceID = spaceTypeSelect.value;
    bayGrid.innerHTML = "";

    if (!spaceID) return;

    // Fetch available bays for the selected space type
    fetch(`getBays.php?spaceID=${spaceID}`)
        .then(res => {
            if (!res.ok) {
                throw new Error('Failed to fetch bays');
            }
            return res.json();
        })
        .then(data => {
            if (data.error) {
                displayError(data.error);
                return;
            }

            // Check if all bays are unavailable
            const allUnavailable = data.every(bay => bay.IsAvailable == 0);

            if (allUnavailable) {
    const infoMessage = document.createElement("div");
    infoMessage.className = "bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4 text-sm col-span-4";

    if (userRole === "Facility Manager") {
        infoMessage.innerHTML = `
            🚫 This parking space is currently full. You can <strong>select a marked bay</strong> to request a <strong>priority booking</strong>.<br>
            🔍 <em>Tip: You can also check other parking spaces or sites for available bays.</em>
        `;
    } else {
        infoMessage.innerHTML = `
            🚫 This parking space is currently full. You can <strong>select a marked bay</strong> to <strong>join the waiting list</strong>.<br>
            🔍 <em>Tip: You can also check other parking spaces or sites for available bays.</em>
        `;
    }

    bayGrid.insertBefore(infoMessage, bayGrid.firstChild);
}



            data.forEach(bay => {
                const div = document.createElement("div");
                div.textContent = bay.IsAvailable == 0 ? `❌ Bay ${bay.BayNumber}` : `Bay ${bay.BayNumber}`;
                div.className = `p-3 text-center rounded font-medium ${
                    bay.IsAvailable == 0
                        ? 'bg-gray-300 text-gray-600 hover:bg-gray-400 cursor-pointer'
                        : 'bg-green-100 text-green-800 hover:bg-green-200 cursor-pointer'
                }`;

                div.addEventListener("click", () => {
                    if (bay.IsAvailable == 1) {
                        window.location.href = `Booking.php?bayID=${bay.BayID}&spaceID=${spaceID}`;
                    } else {
                        if (userRole === 'Facility Manager') {
                            if (confirm('This bay is currently unavailable. Would you like to set this as a priority booking?')) {
                                window.location.href = `Booking.php?bayID=${bay.BayID}&spaceID=${spaceID}&priority=1`;
                            }
                        } else if (userRole === 'Normal User' || userRole === 'Admin' || userRole === 'IT') {
                            if (confirm('This bay is currently unavailable. Would you like to join the waiting list?')) {
                                window.location.href = `JoinWaitingList.php?bayID=${bay.BayID}&spaceID=${spaceID}`;
                            }
                        } else {
                            alert('This bay is currently unavailable.');
                        }
                    }
                });

                bayGrid.appendChild(div);
            });
        })
        .catch(error => {
            console.error(error);
            displayError('An error occurred while fetching parking bays. Please try again.');
        });
});


    // Function to display error messages in the UI
    function displayError(message) {
        const errorElement = document.createElement('div');
        errorElement.textContent = message;
        errorElement.className = 'bg-red-500 text-white p-4 rounded mt-4';
        errorElement.id = 'errorMessage';
        bayGrid.appendChild(errorElement);
    }
});


const userRole = "<?php echo $_SESSION['Role'] ?? 'Guest'; ?>";
const userID = "<?php echo $_SESSION['UserID'] ?? ''; ?>";


</script>


    
<?php include('includes/footer.php'); ?>
</body>