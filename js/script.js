// ===== MOBILE NAVBAR =====
const hamburger = document.getElementById("hamburger");
const navLinks = document.getElementById("navLinks");

if (hamburger && navLinks) {
  hamburger.addEventListener("click", () => {
    navLinks.classList.toggle("open");
    hamburger.classList.toggle("active");
  });

  // Close menu when a nav link is clicked
  document.querySelectorAll("#navLinks a").forEach(link => {
    link.addEventListener("click", () => {
      navLinks.classList.remove("open");
      hamburger.classList.remove("active");
    });
  });
}

// ===== BACK TO TOP BUTTON =====
const backToTopBtn = document.getElementById("backToTop");

window.addEventListener("scroll", () => {
  if (window.scrollY > 300) {
    backToTopBtn.classList.add("visible");
  } else {
    backToTopBtn.classList.remove("visible");
  }
});

function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: "smooth"
  });
}

// ===== FORM SUBMIT =====
const enquiryForm = document.getElementById("enquiryForm");
const formSuccess = document.getElementById("formSuccess");
const submitBtn = document.getElementById("submitBtn");

if (enquiryForm) {
  enquiryForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    // HTML validation check
    if (!enquiryForm.checkValidity()) {
      enquiryForm.reportValidity();
      return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

    const formData = new FormData(enquiryForm);

    try {
      const response = await fetch("submit_enquiry.php", {
        method: "POST",
        body: formData
      });

      const result = await response.json();

      if (result.status === "success") {
        enquiryForm.style.display = "none";
        formSuccess.style.display = "block";
      } else {
        alert(result.message || "Something went wrong. Please try again.");
      }
    } catch (error) {
      console.error("Error:", error);
      alert("Server error. Please try again later.");
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Enquiry';
    }
  });
}

// ===== RESET FORM =====
function resetForm() {
  enquiryForm.reset();
  enquiryForm.style.display = "block";
  formSuccess.style.display = "none";
}