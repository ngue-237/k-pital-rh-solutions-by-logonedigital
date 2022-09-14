const filtersForm = document.querySelector("#filters");

document.querySelectorAll("#filters input").forEach((input) => {
  input.addEventListener("change", () => {
    // on récupère les données du formulaire
    const form = new FormData(filtersForm);

    //on fabrique la query string
    const params = new URLSearchParams();
    form.forEach((value, key) => {
      params.append(key, value);
    });

    //on récupère l'url
    const url = new URL(window.location.href);

    fetch("/offres-emploi" + "?" + params.toString() + "&ajax=1", {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        //get the content to replace
        const content = document.querySelector("#all");

        //replace
        content.innerHTML = data.content;

        history.pushState({}, null, url.pathname + "?" + params.toString());
      })
      .catch((e) => alert(e));
  });
});

//console.log(document.querySelectorAll("#filters option"));

document.querySelectorAll("#filters option").forEach((option) => {
  option.addEventListener("click", () => {
    // on récupère les données du formulaire
    const form = new FormData(filtersForm);

    //on fabrique la query string
    const params = new URLSearchParams();
    form.forEach((value, key) => {
      params.append(key, value);
    });

    //on récupère l'url
    const url = new URL(window.location.href);
    fetch("/offres-emploi" + "?" + "adresse=" + option.value + "&ajax=1", {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        //get the content to replace
        const content = document.querySelector("#all");

        //replace
        content.innerHTML = data.content;

        history.pushState({}, null, url.pathname + "?" + params.toString());
      })
      .catch((e) => alert(e));
  });
});
