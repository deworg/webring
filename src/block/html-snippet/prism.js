import Prism from "prismjs";
import "prismjs/components/prism-markup";

// Import all themes if needed
import "prismjs/themes/prism.css";
import "prismjs/themes/prism-tomorrow.css";
import "prismjs/themes/prism-okaidia.css";

export default function initPrism() {
	// Highlight all enabled blocks
	document.querySelectorAll( ".prism-enabled pre code" ).forEach( ( block ) => {
		Prism.highlightElement( block );
	} );
};
