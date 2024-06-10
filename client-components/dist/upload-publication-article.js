 // Lego version 1.0.0
  import { h, Component } from './lego.min.js'
   
    import { render } from './lego.min.js';
   
    Component.prototype.render = function(state)
    {
      const childs = Array.from(this.childNodes);
      this.__originalChildren = childs.length && !this.__originalChildren?.length ? childs : this.__originalChildren;

       this.__state.slotId = `slot_${performance.now().toString().replace('.','')}_${Math.floor(Math.random() * 1000)}`;
   
      this.setState(state);
      if(!this.__isConnected) return
   
      const rendered = render([
        this.vdom({ state: this.__state }),
        this.vstyle({ state: this.__state }),
      ], this.document);
   
      const slot = this.document.querySelector(`#${this.__state.slotId}`);
      if (slot)
         for (const c of this.__originalChildren)
             slot.appendChild(c);
            
      return rendered;
    };

  
    const state = 
    {
        article_id: 0,
        lang: {},
        allowed_mime_types: null,
        file: null
    };

    const methods =
    {
        setFile(e)
        {
            this.render({ ...this.state, file: e.target.files[0] });
        },

        submit(e)
        {
            e.preventDefault();

            const formData = new FormData();
            formData.append("articleId", this.state.article_id);
            formData.append("file", this.state.file);

            import(AbelMagazine.functionUrl(`/admin/panel/articles/${this.state.article_id}`))
            .then(({ uploadPublication }) => uploadPublication(formData))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .then(() => window.location.reload())
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.finalPdfUploadError));
        }
    };

    function setup()
    {
        this.state = 
        { 
            ...this.state, 
            lang: JSON.parse(this.getAttribute("langJson"))
        };
    }


  const __template = function({ state }) {
    return [  
    h("form", {"onsubmit": this.submit.bind(this), "class": `my-2`}, [
      h("label", {}, [
        h("span", {"class": `font-bold`}, `${state.lang.forms.finalPdfUploadLabel} `),
        h("input", {"type": `file`, "onchange": this.setFile.bind(this), "accept": `${state.allowed_mime_types}`, "class": `file:btn`}, ""),
        h("button", {"type": `submit`, "class": `btn ml-2`}, `${state.lang.forms.send}`)
      ])
    ])
  ]
  }

  const __style = function({ state }) {
    return h('style', {}, `
      
      
    `)
  }

  // -- Lego Core
  export default class Lego extends Component {
    init() {
      this.useShadowDOM = false
      if(typeof state === 'object') this.__state = Object.assign({}, state, this.__state)
      if(typeof methods === 'object') Object.keys(methods).forEach(methodName => this[methodName] = methods[methodName])
      if(typeof connected === 'function') this.connected = connected
      if(typeof setup === 'function') setup.bind(this)()
    }
    get vdom() { return __template }
    get vstyle() { return __style }
  }
  // -- End Lego Core

  
