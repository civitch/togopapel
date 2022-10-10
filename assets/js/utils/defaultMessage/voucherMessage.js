
// Get Part of page
let someBlock = $('#preloaderPart');

//Function charge part of page
export function preLoaderCollect() 
{
    // let someBlock = $("#preloaderPart");
    someBlock.preloader({
        text: "Veuillez patientez",
        percent: 10,
        duration: 2,
    });
}

//Default Function load Sweet alert 2
export function defaultLoad()
{
    console.log("log delete");
}

// Remove Preloader 
export function removePreLoaderCollect() {
    //remove preloader
    someBlock.preloader("remove");
}