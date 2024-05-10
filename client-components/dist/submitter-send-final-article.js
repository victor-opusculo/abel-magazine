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
        article_id: null,
        file: null,
        allowed_mime_types: '',
        button_label: '',
        error: '',

        waiting: false
    }

    const methods =
    {
        setFile(e)
        {
            this.render({ ...this.state, file: e.target.files[0] });
        },

        submit(e)
        {
            e.preventDefault();

            this.render({ ...this.state, waiting: true });

            const fd = new FormData();
            fd.append('file_article_id', this.state.file);

            import(AbelMagazine.functionUrl(`/submitter/panel/articles/${this.state.article_id}`))
            .then(module => module.uploadFinal(fd))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .then(() => window.location.reload())
            .catch(AbelMagazine.Alerts.pushError(this.state.error))
            .finally(() => this.render({ ...this.state, waiting: false }));
        }
    }


  const __template = function({ state }) {
    return [  
    h("form", {"onsubmit": this.submit.bind(this)}, [
      h("input", {"type": `file`, "accept": `${state.allowed_mime_types}`, "onchange": this.setFile.bind(this), "class": `file:btn mr-2`, "required": ``}, ""),
` (PDF)
        `,
      h("button", {"type": `submit`, "class": `btn`, "disabled": state.waiting}, [
        ((state.waiting) ? h("loading-spinner", {"additionalclasses": `invert w-[1em] h-[1em]`}, "") : ''),
`
            ${state.button_label}
        `
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

  
