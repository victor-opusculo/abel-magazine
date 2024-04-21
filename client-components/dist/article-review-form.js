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
        token: null,
        article_id: null,
        is_approved: null,
        assessor_name: '',
        assessor_email: '',
        feedback_message: '',
        lang: {}
    };

    const methods =
    {
        changeField(e)
        {
            if (e.target.type === 'radio')
                this.render({ ...this.render, [ e.target.name ]: Number(e.target.value) });
            else
                this.render({ ...this.render, [ e.target.name ]: e.target.value });

        },

        submit(e)
        {
            e.preventDefault();

            const data = {};

            for (const prop in this.state)
                if (prop !== 'lang')
                    data['assessors_opinions:' + prop] = this.state[prop];

            import(AbelMagazine.functionUrl(`/reviewer/review/${this.state.token}`))
            .then(module => module.saveOpinion(data))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .then(AbelMagazine.Helpers.URLGenerator.goToPageOnSuccess("/"))
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorSavingOpinion));
        }
    }

    function setup()
    {
        this.state = { ...this.state, lang: JSON.parse(this.getAttribute('langJson')) };
    }


  const __template = function({ state }) {
    return [  
    h("form", {"onsubmit": this.submit.bind(this)}, [
      h("ext-label", {"label": `${state.lang.forms.reviewerName}`}, [
        h("input", {"type": `text`, "class": `w-full`, "name": `assessor_name`, "onchange": this.changeField.bind(this), "value": state.assessor_name, "required": ``}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.reviewerEmail}`}, [
        h("input", {"type": `email`, "class": `w-full`, "name": `assessor_email`, "onchange": this.changeField.bind(this), "value": state.assessor_email, "required": ``}, "")
      ]),
      h("div", {"class": `ml-2`}, [
        h("span", {}, `${state.lang.forms.articleFeedbackConclusion}: `),
        h("label", {"class": `mr-4`}, [
          h("input", {"type": `radio`, "name": `is_approved`, "value": `1`, "onchange": this.changeField.bind(this), "checked": state.is_approved === 1, "required": ``}, ""),
` ${state.lang.forms.approved}`
        ]),
        h("label", {}, [
          h("input", {"type": `radio`, "name": `is_approved`, "value": `0`, "onchange": this.changeField.bind(this), "checked": state.is_approved === 0, "required": ``}, ""),
` ${state.lang.forms.disapproved}`
        ])
      ]),
      h("ext-label", {"label": `${state.lang.forms.feedbackMessage}`, "linebreak": `1`}, [
        h("textarea", {"name": `feedback_message`, "onchange": this.changeField.bind(this), "value": state.feedback_message, "rows": `6`, "class": `w-full`}, "")
      ]),
      h("div", {"class": `mt-2 text-center`}, [
        h("button", {"type": `submit`, "class": `btn`}, `${state.lang.forms.send}`)
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

  
